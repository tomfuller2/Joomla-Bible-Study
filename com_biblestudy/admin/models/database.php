<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Import library dependencies
JLoader::register('InstallerModel', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_installer' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR .'extension.php');
JLoader::register('joomlaInstallerScript', JPATH_ADMINISTRATOR . '/components/com_biblestudy/biblestudy.script.php');

/**
 * Installer Manage Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.6
 */
class BiblestudyModelDatabase extends InstallerModel
{
	protected $_context = 'com_installer.discover';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$this->setState('message', $app->getUserState('com_installer.message'));
		$this->setState('extension_message', $app->getUserState('com_installer.extension_message'));
		$app->setUserState('com_installer.message', '');
		$app->setUserState('com_installer.extension_message', '');
		parent::populateState('name', 'asc');
	}

	/**
	 *
	 * Fixes database problems
	 */
	public function fix()
	{
		$changeSet = $this->getItems();
		$changeSet->fix();
		$this->fixSchemaVersion($changeSet);
		$this->fixUpdateVersion();
		$installer = new joomlaInstallerScript();
		$installer->deleteUnexistingFiles();
		$this->fixDefaultTextFilters();
	}

	/**
	 *
	 * Gets the changeset object
	 *
	 * @return  JSchemaChangeset
	 */
	public function getItems()
	{
		$folder = JPATH_ADMINISTRATOR . '/components/com_biblestudy/install/sql/updates/mysql';
		$changeSet = JSchemaChangeset::getInstance(JFactory::getDbo(), $folder); 
		return $changeSet;
	}

	public function getPagination()
	{
		return true;
	}

	/**
	 * Get version from #__schemas table
	 *
	 * @return  mixed  the return value from the query, or null if the query fails
	 * @throws Exception
	 */

	public function getSchemaVersion() {
	$db = JFactory::getDbo();
        $query = $db->getQuery(true);
        //get the extension id
        $extensionquery = 'SELECT extension_id from #__extensions WHERE element = "com_biblestudy"';
        $db->setQuery($extensionquery);
        $db->query();
        $extensionresult = $db->loadResult();
        $schemaquery = 'SELECT version_id from #__schemas WHERE extension_id = "'.$extensionresult.'"';
        $db->setQuery($schemaquery);
        $result = $db->loadResult(); //dump ($result);
        
        $query->select('e.extension_id');
        $query->from('#__extensions as e');
        $query->where('element = "com_biblestudy"');
        $db->setQuery($query);
        $this->extension_id = $db->loadResult();
		
		return $result;
	}

	/**
	 * Fix schema version if wrong
	 *
	 * @param JSchemaChangeSet
	 *
	 * @return   mixed  string schema version if success, false if fail
	 */
	public function fixSchemaVersion($changeSet)
	{
		// Get correct schema version -- last file in array
		$schema = $changeSet->getSchema(); 
		$db = JFactory::getDbo();
		$result = false;

		// Check value. If ok, don't do update
		$version = $this->getSchemaVersion();
		if ($version == $schema)
		{
			$result = $version;
		}
		else
		{
			// Delete old row
			$query = $db->getQuery(true);
			$query->delete($db->qn('#__schemas'));
			$query->where($db->qn('extension_id') . ' = "'.$this->extension_id.'"');
			$db->setQuery($query);
			$db->query();

			// Add new row
			$query = $db->getQuery(true);
			$query->insert($db->qn('#__schemas'));
			$query->set($db->qn('extension_id') . '= "'.$this->extension_id.'"');
			$query->set($db->qn('version_id') . '= ' . $db->q($schema));
			$db->setQuery($query);
			if ($db->query()) {
				$result = $schema;
			}
		}
		return $result;
	}

	/**
	 * Get current version from #__extensions table
	 *
	 * @return  mixed   version if successful, false if fail
	 */

	public function getUpdateVersion()
	{
		$table = JTable::getInstance('Extension');
		$table->load($this->extension_id);
		$cache = new JRegistry($table->manifest_cache); 
		return $cache->get('version'); 
	}

	/**
	 * Fix Joomla version in #__extensions table if wrong (doesn't equal JVersion short version)
	 *
	 * @return   mixed  string update version if success, false if fail
	 */
	public function fixUpdateVersion()
	{
		$table = JTable::getInstance('Extension');
		$table->load($this->extension_id);
		$cache = new JRegistry($table->manifest_cache);
		$updateVersion =  $cache->get('version'); 
       
                $jbsversion = $this->version; 
		//$cmsVersion = new JVersion();
		if ($updateVersion == $jbsversion)
		{
			return $updateVersion;
		}
		else
		{
			$cache->set('version', $jbsversion);
			$table->manifest_cache = $cache->toString();
			if ($table->store())
			{
				return $jbsversion;
			}
			else
			{
				return false;
			}

		}
	}

	/**
	 * For version 2.5.x only
	 * Check if com_config parameters are blank.
	 *
	 * @return  string  default text filters (if any)
	 */
	public function getDefaultTextFilters()
	{
		$table = JTable::getInstance('Extension');
		$table->load($table->find(array('name' => 'com_biblestudy')));
		return $table->params;
	}
	/**
	 * For version 2.5.x only
	 * Check if com_config parameters are blank. If so, populate with com_content text filters.
	 *
	 * @return  mixed  boolean true if params are updated, null otherwise
	 */
	public function fixDefaultTextFilters()
	{
		$table = JTable::getInstance('Extension');
		$table->load($table->find(array('name' => 'com_biblestudy')));

		// Check for empty $config and non-empty content filters
		if (!$table->params)
		{
			// Get filters from com_content and store if you find them
			$contentParams = JComponentHelper::getParams('com_biblestudy');
			if ($contentParams->get('filters'))
			{
				$newParams = new JRegistry();
				$newParams->set('filters', $contentParams->get('filters'));
				$table->params = (string) $newParams;
				$table->store();
				return true;
			}
		}
	}
        
}
