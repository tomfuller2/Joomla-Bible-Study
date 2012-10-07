<?php


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class biblestudyViewseriesedit extends JView
{
	
	function display($tpl = null)
	{
		$document =& JFactory::getDocument();
		$document->addScript(JURI::base().'components/com_biblestudy/js/jquery.js');
		$document->addScript(JURI::base().'components/com_biblestudy/js/noconflict.js');
		$document->addScript(JURI::base().'components/com_biblestudy/js/biblestudy.js');
		JHTML::_('stylesheet', 'icons.css', JURI::base().'components/com_biblestudy/css/');
		$seriesedit		=& $this->get('Data');
		$admin=& $this->get('Admin');
		$admin_params = new JParameter($admin[0]->params);
		$isNew		= ($seriesedit->id < 1);
		$lists = array();
		$teachers =& $this->get('Teacher');
		$types[] 		= JHTML::_('select.option',  '0', '- '. JText::_( 'Select a Teacher' ) .' -' );
		$types 			= array_merge( $types, $teachers );
		$lists['teacher'] = JHTML::_('select.genericlist', $types, 'teacher', 'class="inputbox" size="1" ', 'value', 'text',  $seriesedit->teacher );
		
		
		$seriesImagePath = JPATH_SITE.'/images/'.$admin_params->get('series_imagefolder', 'stories');
		$seriesImageList = JFolder::files($seriesImagePath, null, null, null, array('index.html'));
		array_unshift($seriesImageList, '- '.JText::_('No Image').' -');
		
		foreach($seriesImageList as $key=>$value) {
			$seriesImageOptions[] = JHTML::_('select.option', $value, $value);
		}
		$seriesImageOptions[0]->value = 0; //Set the value of the "- No Image -" to 0. Makes it easier for jquery
		
		$lists['series_thumbnail'] = JHTML::_('select.genericlist',  $seriesImageOptions, 'series_thumbnail', 'class="imgChoose" size="1"', 'value', 'text',  $seriesedit->series_thumbnail);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Series Edit' ).': <small><small>[ ' . $text.' ]</small></small>', 'series.png' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		jimport( 'joomla.i18n.help' );
		JToolBarHelper::help( 'biblestudy', true );
		$this->assignRef('admin_params', $admin_params);
		$this->assignRef('lists', $lists);
		$this->assignRef('seriesedit',		$seriesedit);

		parent::display($tpl);
	}
}
?>