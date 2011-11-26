<?php
/**
 * @version     $Id$
 * @package     com_biblestudy
 * @license     GNU/GPL
 */

//No Direct Access
defined('_JEXEC') or die();

require_once (JPATH_SITE  .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_biblestudy' .DIRECTORY_SEPARATOR. 'lib' .DIRECTORY_SEPARATOR. 'biblestudy.defines.php');
require_once (JPATH_ROOT  .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_biblestudy' .DIRECTORY_SEPARATOR. 'lib' .DIRECTORY_SEPARATOR. 'biblestudy.images.class.php');
require_once (JPATH_ROOT  .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_biblestudy' .DIRECTORY_SEPARATOR. 'lib' .DIRECTORY_SEPARATOR. 'biblestudy.admin.class.php');
jimport( 'joomla.application.component.view' );

class biblestudyViewLandingpage extends JView {

	/**
	 * Landing Page view display method
	 * @return void
	 **/
	function display($tpl = null) {
		$mainframe =& JFactory::getApplication(); $option = JRequest::getCmd('option');
		$path1 = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_biblestudy'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR;
		include_once($path1.'image.php');
		//Load the Admin settings and params from the template
		$this->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers');
		$document =& JFactory::getDocument();
		$model =& $this->getModel();

		$t = JRequest::getInt('t','get',1);
		if (!$t) {
			$t = 1;
		}

		$template = $this->get('template');

		// Convert parameter fields to objects.
		$registry = new JRegistry;
		$registry->loadJSON($template[0]->params);
		$params = $registry;
		$admin = $this->get('Admin');

		// Convert parameter fields to objects.
		$registry = new JRegistry;
		$registry->loadJSON($admin[0]->params);
		$this->admin_params = $registry;

		$document =& JFactory::getDocument();
		$document->addScript(JURI::base().'components'.DIRECTORY_SEPARATOR.'com_biblestudy'.DIRECTORY_SEPARATOR.'tooltip.js');
		$stylesheet = JURI::base().'components/com_biblestudy/assets/css/biblestudy.css';
		$document->addStyleSheet($stylesheet);

		//Import Scripts
		$document->addScript(JURI::base().'administrator/components/com_biblestudy/js/jquery.js');
		$document->addScript(JURI::base().'administrator/components/com_biblestudy/js/biblestudy.js');

		//Import Stylesheets
		$document->addStylesheet(JURI::base().'administrator/components/com_biblestudy/css/general.css');

		$url = $params->get('stylesheet');
		if ($url) {$document->addStyleSheet($url);}

		$uri				=& JFactory::getURI();
		$filter_topic		= $mainframe->getUserStateFromRequest( $option.'filter_topic', 'filter_topic',0,'int' );
		$filter_book		= $mainframe->getUserStateFromRequest( $option.'filter_book', 'filter_book',0,'int' );
		$filter_teacher		= $mainframe->getUserStateFromRequest( $option.'filter_teacher','filter_teacher',0,'int' );
		$filter_series		= $mainframe->getUserStateFromRequest( $option.'filter_series',	'filter_series',0,'int' );
		$filter_messagetype	= $mainframe->getUserStateFromRequest( $option.'filter_messagetype','filter_messagetype',0,'int' );
		$filter_year		= $mainframe->getUserStateFromRequest( $option.'filter_year','filter_year',0,'int' );
		$filter_location	= $mainframe->getuserStateFromRequest( $option.'filter_location','filter_location',0,'int');
		$filter_orders		= $mainframe->getUserStateFromRequest( $option.'filter_orders','filter_orders','DESC','word' );
		$search				= JString::strtolower($mainframe->getUserStateFromRequest( $option.'search','search','','string'));

		//		$results = $this->get('Data');
		$adminrows = new JBSAdmin();
		//        $items = $adminrows->showRows($results);
		$total = $this->get('Total');

		$pagination = $this->get('Pagination');

		$menu =& JSite::getMenu();
		$item =& $menu->getActive();
		//Get the main study list image
		$images = new jbsImages();
		$main = $images->mainStudyImage();

		$this->assignRef('request_url',	$uri->toString());
		$this->assignRef('params', $params);
		parent::display($tpl);
	}
}
