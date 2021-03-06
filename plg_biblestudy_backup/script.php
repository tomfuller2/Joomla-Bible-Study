<?php
/**
 * Part of Joomla BibleStudy Package
 *
 * @package    BibleStudy.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
// No direct access to this file
defined('_JEXEC') or die;

/**
 * Script file of JBSBackup component
 *
 * @package     BibleStudy
 * @subpackage  Plugin.JBSBackup
 * @since       7.1.0
 */
class PlgSystemJBSBackupInstallerScript
{
	/** @var  string If it is 7.0.0 code */
	public $is700;

	/**
	 * method to install the component
	 *
	 * @param   string  $parent  is the class calling this method
	 *
	 * @return void
	 */
	public function install($parent)
	{

	}

	/**
	 * method to uninstall the component
	 *
	 * @param   string  $parent  is the class calling this method
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('JBS_PLG_BACKUP_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @param   string  $parent  is the class calling this method
	 *
	 * @return void
	 */
	public function update($parent)
	{
		// $parent is the class calling this method
		// check to see if we are dealing with version 7.0.0 and create the update table if needed
		$db = JFactory::getDBO();

		// First see if there is an update table
		$tables      = $db->getTableList();
		$prefix      = $db->getPrefix();
		$updatetable = $prefix . 'jbsbackup_timeset';
		$updatefound = false;
		$this->is700 = false;

		foreach ($tables as $table)
		{
			if ($table == $updatetable)
			{
				$updatefound = true;
			}
		}
		if (!$updatefound)
		{
			// Do the query here to create the table. This will tell Joomla to update the db from this version on
			$query = "CREATE TABLE IF NOT EXISTS `#__jbsbackup_timeset` (
					`timeset` varchar(14) NOT NULL DEFAULT '',
					`backup` varchar(14) DEFAULT NULL,
					PRIMARY KEY (`timeset`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8";
			$db->setQuery($query);
			$db->execute();
			$data          = new stdClass;
			$data->timeset = 1281646339;
			$data->backup  = 1281646339;
			$db->insertObject('#__jbsbackup_timeset', $data);
		}
		echo '<p>' . JText::_('JBS_PLG_BACKUP_UPDATE_TEXT') . '</p>';
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string  $type    is the type of change (install, update or discover_install)
	 * @param   string  $parent  is the class calling this method
	 *
	 * @return void
	 */
	public function preflight($type, $parent)
	{
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string  $type    is the type of change (install, update or discover_install)
	 * @param   string  $parent  is the class calling this method
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
	}

}
