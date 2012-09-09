<?php

/**
 * Controller for Migration
 * @package BibleStudy.Admin
 * @Copyright (C) 2007 - 2011 Joomla Bible Study Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.JoomlaBibleStudy.org
 * */
// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
jimport('joomla.html.parameter');
include_once(BIBLESTUDY_PATH_ADMIN_LIB . DIRECTORY_SEPARATOR . 'biblestudy.restore.php');
include_once(BIBLESTUDY_PATH_ADMIN_LIB . DIRECTORY_SEPARATOR . 'biblestudy.backup.php');
include_once(BIBLESTUDY_PATH_ADMIN_LIB . DIRECTORY_SEPARATOR . 'biblestudy.migrate.php');
JLoader::register('Com_BiblestudyInstallerScript', JPATH_ADMINISTRATOR . '/components/com_biblestudy/biblestudy.script.php');

/**
 * JBS Export Migration Controller
 *
 * @package BibleStudy.Admin
 * @since 7.1.0
 */
class BiblestudyControllerMigration extends JController {

    /**
     * Method to display the view
     * @param boolon $cachable
     * @param boolon $urlparams
     *
     * @access	public
     */
    public function display($cachable = false, $urlparams = false) {

        JRequest::setVar('view', JRequest::getCmd('view', 'admin'));
        $application = JFactory::getApplication();
        JRequest::setVar('migrationdone', '0', 'get');
        $task = JRequest::getWord('task', '', '');
        $oldprefix = JRequest::getInt('oldprefix', '', 'post');
        $run = 0;
        $run = JRequest::getInt('run', '', 'get');
        $import = JRequest::getVar('file', '', 'post');

        if ($task == 'export' && ($run == 1 || $run == 2)) {
            $export = new JBSExport();
            if (!$result = $export->exportdb($run)) {
                $msg = JText::_('JBS_CMN_OPERATION_FAILED');
                $this->setRedirect('index.php?option=com_biblestudy&view=admin&layout=edit&id=1', $msg);
            } elseif ($run == 2) {
                if (!$result) {
                    $msg = $result;
                } else {
                    $msg = JText::_('JBS_CMN_OPERATION_SUCCESSFUL');
                }
                $this->setRedirect('index.php?option=com_biblestudy&view=admin&layout=edit&id=1', $msg);
            }
        }

        if ($task == 'migrate' && $run == 1 && !$oldprefix) {

            $migrate = new JBSMigrate();
            $migration = $migrate->migrate();
            if ($migration) {
                $application->enqueueMessage('' . JText::_('JBS_CMN_OPERATION_SUCCESSFUL') . '');
                JRequest::setVar('migrationdone', '1', 'get');
                $errors = JRequest::getVar('jbsmessages', $jbsmessages, 'get', 'array');
            } else {
                //$application->enqueueMessage('' . JText::_('JBS_CMN_OPERATION_FAILED') . $migration);
                JError::raiseWarning('403', JText::_('JBS_CMN_OPERATION_FAILED'));
            }
        }

        if ($task == 'import') {
            $importjbs = $this->import();
        }
        parent::display($tpl);

        return $this;
    }

    /**
     * Import function from the backup page
     * @since 7.1.0
     */
    public function import() {
        $application = JFactory::getApplication();
        $import = new JBSImport();
        $parent = FALSE;
        $result = $import->importdb($parent);
        if ($result === true) {
            $application->enqueueMessage('' . JText::_('JBS_CMN_OPERATION_SUCCESSFUL') . '');
        } elseif ($result === false) {

        } else {
            $application->enqueueMessage('' . $result . '');
        }
        $this->setRedirect('index.php?option=com_biblestudy&view=admin&layout=edit&id=1', $msg);
    }

    /**
     * Do the import
     *
     * @param boolean $parent Sorece of info
     * @param boolean $cachable
     * @param boolean $urlparams Description
     */
    public function doimport($parent, $cachable = false, $urlparams = false) {
        $copysuccess = false;
        if ($parent !== FALSE):
            $parent = TRUE;
        endif;
        //This should be where the form admin/form_migrate comes to with either the file select box or the tmp folder input field
        $application = JFactory::getApplication();
        JRequest::setVar('view', JRequest::getCmd('view', 'admin'));

        //Add commands to move tables from old prefix to new
        $oldprefix = '';
        $oldprefix = JRequest::getWord('oldprefix', '', 'post');

        if ($oldprefix) {
            $tablescopied = $this->copyTables($oldprefix);
            //if error
            //check for empty array - if not, print results
            if (empty($tablescopied)) {
                $copysuccess = 1;
            } else {
                $copysuccess = false;
                print_r($tablescopied);
            }
        } else {
            $import = new JBSImport();
            $result = $import->importdb($parent);
        }
        if ($result || $copysuccess) {
            $migrate = new JBSMigrate();
            $migration = $migrate->migrate();
            $messages = JText::_('JBS_CMN_NO_ERROR_MESSAGES_AVAILABLE');
            if (!empty($migration)) {
                $messages = implode('<br>', $migration);
            }
            //Final step is to fix assets
            $this->fixAssets();
            $installer = new Com_BiblestudyInstallerScript();
            $installer->deleteUnexistingFiles();  // Need to Update first deleat files of the new template do to them not in the biblestudy xml
            $installer->fixMenus();
            $installer->fixImagePaths();
            if ($migration) {
                $application->enqueueMessage('' . JText::_('JBS_CMN_OPERATION_SUCCESSFUL') . JText::_('JBS_IBM_REVIEW_ADMIN_TEMPLATE') . $messages, 'message');
                JRequest::setVar('migrationdone', '1', 'get');
            } else {
                //$application->enqueueMessage('' . JText::_('JBS_CMN_DATABASE_NOT_MIGRATED') . $messages . '', 'message');
                JError::raiseWarning('403', JText::_('JBS_CMN_DATABASE_NOT_MIGRATED'));
            }
            JRequest::setVar('migrationdone', '1', 'get');
        } else {
            //$application->enqueueMessage('' . JText::_('JBS_CMN_DATABASE_NOT_COPIED') . $messages . '', 'message');
            JError::raiseWarning('403', JText::_('JBS_CMN_DATABASE_NOT_COPIED'));
        }
        $this->setRedirect('index.php?option=com_biblestudy&task=admin.edit&id=1');
    }

    /**
     * Perform DB check
     *
     * @param array $query
     * @return string|boolean
     */
    public function performdb($query) {
        $db = JFactory::getDBO();
        $results = false;
        $db->setQuery($query);
        $db->query();
        if ($db->getErrorNum() != 0) {
            $results = JText::_('JBS_IBM_DB_ERROR') . ': ' . $db->getErrorNum() . "<br /><font color=\"red\">";
            $results .= $db->stderr(true);
            $results .= "</font>";
            return $results;
        } else {
            $results = false;
            return $results;
        }
    }

    /**
     * Copy Old Tables to new Joomla! Tables
     *
     * @param string $oldprefix
     * @return array
     */
    public function copyTables($oldprefix) {
        //create table tablename_new like tablename; -> this will copy the structure...
        //insert into tablename_new select * from tablename; -> this would copy all the data
        $results = array();
        $db = JFactory::getDBO();
        $tables = $db->getTableList();
        $prefix = $db->getPrefix();
        foreach ($tables as $table) {
            $isjbs = substr_count($table, $oldprefix . 'bsms');
            if ($isjbs) {
                $oldlength = strlen($oldprefix);
                $newsubtablename = substr($table, $oldlength);
                $newtablename = $prefix . $newsubtablename;
                $results = array();
                $query = 'DROP TABLE IF EXISTS ' . $newtablename;
                $result = $this->performdb($query);
                if ($result) {
                    $results[] = $result;
                }
                $query = 'CREATE TABLE ' . $newtablename . ' LIKE ' . $table;
                $result = $this->performdb($query);
                if ($result) {
                    $results[] = $result;
                }
                $query = 'INSERT INTO ' . $newtablename . ' SELECT * FROM ' . $table;
                $result = $this->performdb($query);
                if ($result) {
                    $results[] = $result;
                }
            }
        }
        return $results;
    }

    /**
     * Fix Assets Table
     *
     * @return boolean
     */
    public function fixAssets() {
        require_once(BIBLESTUDY_PATH_ADMIN_LIB . DIRECTORY_SEPARATOR . 'biblestudy.assets.php');
        $asset = new fixJBSAssets();
        $asset->fixAssets();
        return true;
    }

}

// end of class
