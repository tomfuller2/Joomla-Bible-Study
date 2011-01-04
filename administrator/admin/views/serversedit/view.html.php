<?php


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );


class biblestudyViewserversedit extends JView
{
	
	function display($tpl = null)
	{
		
		$serversedit		=& $this->get('Data');
		$isNew		= ($serversedit->id < 1);
		JHTML::_('stylesheet', 'icons.css', JURI::base().'components/com_biblestudy/css/');
		$text = $isNew ? JText::_( 'JBS_CMN_NEW' ) : JText::_( 'JBS_CMN_EDIT' );
		JToolBarHelper::title(   JText::_( 'JBS_SVR_SERVER_EDIT' ).': <small><small>[ ' . $text.' ]</small></small>', 'servers.png' );
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

		$this->assignRef('serversedit',		$serversedit);

		parent::display($tpl);
	}
}
?>