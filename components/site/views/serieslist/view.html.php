<?php
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class biblestudyViewserieslist extends JView {
	
	/**
	 * studieslist view display method
	 * @return void
	 **/
	function display($tpl = null) {
		global $mainframe, $option;
		$path1 = JPATH_SITE.DS.'components'.DS.'com_biblestudy'.DS.'helpers'.DS;
		include_once($path1.'image.php');
		$this->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers');
		$document =& JFactory::getDocument();
		$model =& $this->getModel();
		$admin=& $this->get('Admin');
		$admin_params = new JParameter($admin[0]->params);
		$this->assignRef('admin_params', $admin_params);
		$this->assignRef('admin', $admin);
		$params 			=& $mainframe->getPageParameters();
		//dump ($params, 'params: ');
		$templatemenuid = $params->get('templatemenuid');
		if (!$templatemenuid){$templatemenuid = 1;}
		JRequest::setVar( 'templatemenuid', $templatemenuid, 'get');
		$template = $this->get('Template');
		$params = new JParameter($template[0]->params);
		//dump ($template, 'template: ');
		$document =& JFactory::getDocument();
		$document->addScript(JURI::base().'components'.DS.'com_biblestudy'.DS.'tooltip.js');
		//$document->addStyleSheet(JURI::base().'components'.DS.'com_biblestudy'.DS.'tooltip.css');
		$document->addStyleSheet(JURI::base().'components'.DS.'com_biblestudy'.DS.'assets'.DS.'css'.DS.'biblestudy.css');
		
		//Import Scripts
		$document->addScript(JURI::base().'administrator/components/com_biblestudy/js/jquery.js');
		$document->addScript(JURI::base().'administrator/components/com_biblestudy/js/biblestudy.js');
		$document->addScript(JURI::base().'components/com_biblestudy/tooltip.js');
		
		//Import Stylesheets
		$document->addStylesheet(JURI::base().'administrator/components/com_biblestudy/css/general.css');
		
		$url = $params->get('stylesheet');
		if ($url) {$document->addStyleSheet($url);}
		//Initialize templating class
		//$tmplEninge = $this->loadHelper('templates.helper');
		//$tmplEngine =& bibleStudyTemplate::getInstance();
	
		//dump ($params, 'params: ');
		$uri				=& JFactory::getURI();
		$filter_series		= $mainframe->getUserStateFromRequest( $option.'filter_series',	'filter_series',0,'int' );
		$filter_year		= $mainframe->getUserStateFromRequest( $option.'filter_year','filter_year',0,'int' );
		$filter_orders		= $mainframe->getUserStateFromRequest( $option.'filter_orders','filter_orders','DESC','word' );
		//$search				= JString::strtolower($mainframe->getUserStateFromRequest( $option.'search','search','','string'));
/*
		//Retrieve Parameters
		$tmplStudiesList = $params->get('tmplStudiesList');
		$tmplSingleStudyList = $params->get('tmplSingleStudyList');

		//Retrieve the tags that are used in the current template
		$tmplStudiesList = $tmplEngine->loadTagList(null, $tmplStudiesList);
		$tmplSingleStudyList = $tmplEngine->loadTagList(null, $tmplSingleStudyList, true);

		//@todo Find a way to assign the Return fo the buildSqlSelect to the Model Var
		$model->_select = $tmplEngine->buildSqlSELECT($tmplSingleStudyList);
*/		//dump ($template, 'template: ');
		$items = $this->get('Data');
		$total = $this->get('Total');
		//dump ($items, 'items: ');
		$pagination = $this->get('Pagination');
		//$teachers = $this->get('Teachers');
		$series = $this->get('Series');
		$orders = $this->get('Orders');
		
        //This is the helper for scripture formatting
        $scripture_call = Jview::loadHelper('scripture');
		//end scripture helper
		$this->assignRef('template', $template);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('order', $orders);
		//$this->assignRef('topic', $topics);
		$menu =& JSite::getMenu();
		$item =& $menu->getActive();
//dump ($admin[0]->main, 'main: ');
		//Get the main study list image
		if ($admin[0]->main == '- Default Image -'){$i_path = 'components/com_biblestudy/images/openbible.png'; $main = getImage($i_path);}
		else 
		{
				if ($admin[0]->main && !$admin_params->get('media_imagefolder')) { $i_path = 'components/com_biblestudy/images/'.$admin[0]->main; }
				if ($admin[0]->main && $admin_params->get('media_imagefolder')) { $i_path = 'images'.DS.$admin_params->get('media_imagefolder').DS.$admin[0]->main;}
		$main = getImage($i_path);
		}
		if ($admin[0]->main == '- Default Image -' && $admin_params->get('media_imagefolder')) 
		{
			$i_path = 'components/com_biblestudy/images/openbible.png'; $main = getImage($i_path);
		}
	  	$this->assignRef('main', $main);
	  	
		//Build Series List for drop down menu
		$types3[] 		= JHTML::_('select.option',  '0', '- '. JText::_( 'Select a Series' ) .' -' );
		$types3 			= array_merge( $types3, $series );
		$lists['seriesid']	= JHTML::_('select.genericlist',   $types3, 'filter_series', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', "$filter_series" );

		//build orders
		$ord[] 		= JHTML::_('select.option',  '0', '- '. JText::_( 'Select an Order' ) .' -' );
		$orders 			= array_merge( $ord, $orders );
		$lists['sorting']	= JHTML::_('select.genericlist',   $orders, 'filter_orders', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', "$filter_orders" );


		//Build order
		$ord[]		= JHTML::_('select.option', '0', '- '. JTEXT::_('Select an Order') . ' -');
		$ord		= array_merge($ord, $orders);
		$lists['orders'] = JHTML::_('select.genericlist', $ord, 'filter_orders', 'class="inputbox" size="1" oncchange="this.form.submit()"', 'value', 'text', "filter_orders");
		
		//$lists['search']= $search;
		
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);

		$this->assignRef('request_url',	$uri->toString());
		$this->assignRef('params', $params);
		parent::display($tpl);
	}
}
?>