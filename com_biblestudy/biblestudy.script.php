<?php
/**
 * @package    BibleStudy.Admin
 * @copyright  2007 - 2013 Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');


/**
 * BibleStudy Install Script
 *
 * @package  BibleStudy.Admin
 * @since    7.0.0
 */
class Com_BiblestudyInstallerScript
{

	/** @var string The component's name */
	protected $biblestudy_extension = 'com_biblestudy';

	/** @var string Path to Mysql files */
	public $filePath = '/components/com_biblestudy/install/sql/updates/mysql';

	protected $versions = array(
		'PHP'     => array(
			'5.3' => '5.3.1',
			'0'   => '5.4.23' // Preferred version
		),
		'MySQL'   => array(
			'5.1' => '5.1',
			'0'   => '5.5' // Preferred version
		),
		'Joomla!' => array(
			'3.4' => '3.4.1',
			'3.3' => '3.3.6',
			'2.5' => '2.5.28',
			'0'   => '3.4.1' // Preferred version
		)
	);

	protected $extensions = array('dom', 'gd', 'json', 'pcre', 'SimpleXML');

	/**
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 *
	 * @param   string          $type    Type of install
	 * @param   JInstallerFile  $parent  Where it is coming from
	 *
	 * @return boolean
	 */
	public function preflight($type, $parent)
	{
		$parent   = $parent->getParent();
		$manifest = $parent->getManifest();

		// Prevent installation if requirements are not met.
		if (!$this->checkRequirements($manifest->version))
		{
			return false;
		}

		$adminPath = $parent->getPath('extension_administrator');
		$sitePath  = $parent->getPath('extension_site');

		if (is_file($adminPath . '/admin.biblestudy.php'))
		{
			// JBSM 8.0 or older release found, clean up the directories.
			static $ignoreAdmin = array('index.html', 'biblestudy.xml', 'archive');
			if (is_file($adminPath . '/install.script.php'))
			{
				// JBSM 6.2 or older release..
				$ignoreAdmin[] = 'install.script.php';
				$ignoreAdmin[] = 'admin.biblestudy.php';
			}
			static $ignoreSite = array('index.html', 'biblestudy.php', 'router.php', 'COPYRIGHT.php', 'CHANGELOG.php');
			$this->deleteFolder($adminPath, $ignoreAdmin);
			$this->deleteFolder($sitePath, $ignoreSite);
		}

		return true;
	}

	/**
	 * Install
	 *
	 * @param   JInstallerFile  $parent  Where call is coming from
	 *
	 * @return  bool
	 */
	public function install($parent)
	{
		// Delete all cached files.
		$cacheDir = JPATH_CACHE . '/biblestudy';
		if (is_dir($cacheDir))
		{
			JFolder::delete($cacheDir);
		}
		JFolder::create($cacheDir);

		return true;
	}

	/**
	 * Rout to Install
	 *
	 * @param   JInstallerFile  $parent  ?
	 *
	 * @return bool
	 */
	public function discover_install($parent)
	{
		return self::install($parent);
	}

	/**
	 * Update will go to install
	 *
	 * @param   JInstallerFile  $parent  ?
	 *
	 * @return bool
	 */
	public function update($parent)
	{
		return self::install($parent);
	}

	/**
	 * Uninstall rout to JBSMModelInstall
	 *
	 * @param   JInstallerFile  $parent  ?
	 *
	 * @return bool
	 */
	public function uninstall($parent)
	{
		$adminpath = $parent->getParent()->getPath('extension_administrator');
		$model     = "{$adminpath}/models/install.php";
		if (file_exists($model))
		{
			require_once($model);
			$installer = new BibleStudyModelInstall;
			$installer->uninstall();
		}

		return true;
	}

	/**
	 * Post Flight
	 *
	 * @param   string          $type    Type of install
	 * @param   JInstallerFile  $parent  Where it is coming from
	 *
	 * @return   void
	 */
	public function postflight($type, $parent)
	{
		// Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');

		if (!JFile::exists(JPATH_SITE . '/images/biblestudy/logo.png'))
		{
			// Copy the images to the new folder
			JFolder::copy('/media/com_biblestudy/images', 'images/biblestudy/', JPATH_SITE, true);
		}

		// An redirect to a new location after the install is completed.
		$controller = JControllerLegacy::getInstance('Biblestudy');
		$controller->setRedirect(JUri::base() . 'index.php?option=com_biblestudy&view=install&scanstate=start&' . JSession::getFormToken() . '=1');
		$controller->redirect();
	}

	/**
	 * Check Requirements
	 *
	 * @param   string  $version  JBSM version to check for.
	 *
	 * @return bool
	 */
	public function checkRequirements($version)
	{
		$db   = JFactory::getDbo();
		$pass = $this->checkVersion('PHP', phpversion());
		$pass &= $this->checkVersion('Joomla!', JVERSION);
		$pass &= $this->checkVersion('MySQL', $db->getVersion());
		$pass &= $this->checkDbo($db->name, array('mysql', 'mysqli'));
		$pass &= $this->checkExtensions($this->extensions);
		$pass &= $this->checkJBSM($version);

		return $pass;
	}

	// Internal functions

	/**
	 * Check Database Driver
	 *
	 * @param   string  $name   Driver
	 * @param   array   $types  Array of drivers supported
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 */
	protected function checkDbo($name, $types)
	{
		$app = JFactory::getApplication();

		if (in_array($name, $types))
		{
			return true;
		}

		$app->enqueueMessage(sprintf("Database driver '%s' is not supported. Please use MySQL instead.", $name), 'notice');

		return false;
	}

	/**
	 * Check PHP Extension Requirement
	 *
	 * @param   array  $extensions  Array of version to look for
	 *
	 * @return int
	 *
	 * @throws \Exception
	 */
	protected function checkExtensions($extensions)
	{
		$app = JFactory::getApplication();

		$pass = 1;
		foreach ($extensions as $name)
		{
			if (!extension_loaded($name))
			{
				$pass = 0;
				$app->enqueueMessage(sprintf("Required PHP extension '%s' is missing. Please install it into your system.", $name), 'notice');
			}
		}

		return $pass;
	}

	/**
	 * Check Verions of JBSM
	 *
	 * @param   string  $name     Name of version
	 * @param   string  $version  Version to look for
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 */
	protected function checkVersion($name, $version)
	{
		$app = JFactory::getApplication();

		$major = $minor = 0;
		foreach ($this->versions[$name] as $major => $minor)
		{
			if (!$major || version_compare($version, $major, '<'))
			{
				continue;
			}

			if (version_compare($version, $minor, '>='))
			{
				return true;
			}

			break;
		}
		if (!$major)
		{
			$minor = reset($this->versions[$name]);
		}
		$recommended = end($this->versions[$name]);
		$app->enqueueMessage(
			sprintf("%s %s is not supported. Minimum required version is %s %s, but it is higly recommended to use %s %s or later.",
			$name, $version, $name, $minor, $name, $recommended
			), 'notice'
		);

		return false;
	}

	/**
	 * Check the installed version of JBSM
	 *
	 * @param   string  $version  JBSM Verstion to check for
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 */
	protected function checkJBSM($version)
	{
		$app = JFactory::getApplication();

		// Allways load JBSM API if it exists.
		$api = JPATH_ADMINISTRATOR . '/components/com_biblestudy/api.php';

		if (file_exists($api))
		{
			require_once $api;
		}

		$db = JFactory::getDBO();

		// Check if JBSM can be found from the database
		$table = $db->getPrefix() . 'bsms_update';
		$db->setQuery("SHOW TABLES LIKE {$db->quote($table)}");

		if ($db->loadResult() != $table)
		{
			return true;
		}

		// Get installed JBSM version
		$db->setQuery("SELECT version FROM {$db->quoteName($table)} ORDER BY `id` DESC", 0, 1);
		$installed = $db->loadResult();

		if (!$installed)
		{
			return true;
		}

		// Always allow upgrade to the newer version
		if (version_compare($version, $installed, '>='))
		{
			return true;
		}

		$app->enqueueMessage(sprintf('Sorry, it is not possible to downgrade BibleStudy %s to version %s.', $installed, $version), 'notice');

		return false;
	}

	/**
	 * Delete Files
	 *
	 * @param   string  $path    Path of File
	 * @param   array   $ignore  Array of files to Ignore
	 *
	 * @return void
	 */
	public function deleteFiles($path, $ignore = array())
	{
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

		if (JFolder::exists($path))
		{
			foreach (JFolder::files($path, '.', false, true, $ignore) as $file)
			{
				if (JFile::exists($file))
				{
					JFile::delete($file);
				}
			}
		}
	}

	/**
	 * Delete Folders
	 *
	 * @param   array  $path    Path to folders
	 * @param   array  $ignore  Ingnore array of files
	 *
	 * @return void;
	 */
	public function deleteFolders($path, $ignore = array())
	{
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

		if (JFolder::exists($path))
		{
			foreach (JFolder::folders($path, '.', false, true, $ignore) as $folder)
			{
				if (JFolder::exists($folder))
				{
					JFolder::delete($folder);
				}
			}
		}
	}

	/**
	 * Delete Folder
	 *
	 * @param   string  $path    Path of folder
	 * @param   array   $ignore  Ignore list
	 *
	 * @return void
	 */
	public function deleteFolder($path, $ignore = array())
	{
		$this->deleteFiles($path, $ignore);
		$this->deleteFolders($path, $ignore);
	}
}
