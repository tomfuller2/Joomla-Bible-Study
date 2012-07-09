<?php

/**
 * @package BibleStudy.Admin
 * @Copyright (C) 2007 - 2011 Joomla Bible Study Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.JoomlaBibleStudy.org
 * */
//No Direct Access
defined('_JEXEC') or die;

// Version information
define('BIBLESTUDY_VERSION', '7.1.0');
define('BIBLESTUDY_VERSION_DATE', '2012-02-12');
define('BIBLESTUDY_VERSION_BUILD', '710');

// Default values
define('BIBLESTUDY_COMPONENT_NAME', 'com_biblestudy');
define('BIBLESTUDY_LANGUAGE_DEFAULT', 'english');
define('BIBLESTUDY_TEMPLATE_DEFAULT', 'default');

// File system paths
define('BIBLESTUDY_COMPONENT_RELPATH', 'components' . DIRECTORY_SEPARATOR . BIBLESTUDY_COMPONENT_NAME);

define('BIBLESTUDY_ROOT_PATH', JPATH_ROOT);
define('BIBLESTUDY_ROOT_PATH_ADMIN', JPATH_ADMINISTRATOR);

define('BIBLESTUDY_PATH', JPATH_SITE . DIRECTORY_SEPARATOR . BIBLESTUDY_COMPONENT_RELPATH);
define('BIBLESTUDY_PATH_LIB', BIBLESTUDY_PATH . DIRECTORY_SEPARATOR . 'lib');
define('BIBLESTUDY_PATH_TEMPLATE', BIBLESTUDY_PATH . DIRECTORY_SEPARATOR . 'template');
define('BIBLESTUDY_PATH_TEMPLATE_DEFAULT', BIBLESTUDY_PATH_TEMPLATE . DIRECTORY_SEPARATOR . BIBLESTUDY_TEMPLATE_DEFAULT);

define('BIBLESTUDY_PATH_ADMIN', BIBLESTUDY_ROOT_PATH_ADMIN . DIRECTORY_SEPARATOR . BIBLESTUDY_COMPONENT_RELPATH);
define('BIBLESTUDY_PATH_ADMIN_LIB', BIBLESTUDY_PATH_ADMIN . DIRECTORY_SEPARATOR . 'lib');
define('BIBLESTUDY_PATH_ADMIN_HELPERS', BIBLESTUDY_PATH_ADMIN . DIRECTORY_SEPARATOR . 'helpers');
define('BIBLESTUDY_PATH_ADMIN_LANGUAGE', BIBLESTUDY_PATH_ADMIN . DIRECTORY_SEPARATOR . 'language');
define('BIBLESTUDY_PATH_ADMIN_INSTALL', BIBLESTUDY_PATH_ADMIN . DIRECTORY_SEPARATOR . 'install');
define('BIBLESTUDY_PATH_ADMIN_IMAGES', BIBLESTUDY_PATH_ADMIN . DIRECTORY_SEPARATOR . 'images');


//define('BIBLESTUDY_FILE_INSTALL', BIBLESTUDY_PATH_ADMIN .DIRECTORY_SEPARATOR. 'biblestudy.xml');
// URLs
// Constants
// Minimum version requirements
DEFINE('BIBLESTUDY_MIN_PHP', '5.0.3');
DEFINE('BIBLESTUDY_MIN_MYSQL', '4.1.19');

// Time related
define('BIBLESTUDY_SECONDS_IN_HOUR', 3600);
define('BIBLESTUDY_SECONDS_IN_YEAR', 31536000);

// Database defines
define('BIBLESTUDY_DB_MISSING_COLUMN', 1054);