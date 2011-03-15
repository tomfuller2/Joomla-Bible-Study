<?php
/**
 * @version     $Id: view.html.php 1466 2011-01-31 23:13:03Z bcordis $
 * @package     com_biblestudy
 * @license     GNU/GPL
 */

//No Direct Access
defined('_JEXEC') or die();
require_once (JPATH_ADMINISTRATOR  .DS. 'components' .DS. 'com_biblestudy' .DS. 'lib' .DS. 'biblestudy.defines.php');
jimport( 'joomla.application.component.view' );


class biblestudyViewmediaedit extends JView
{

    protected $form;
    protected $item;
    protected $state;
    protected $admin;
	
	function display($tpl = null)
	{
        $this->form = $this->get("Form");
        $this->item = $this->get("Item");
		
		$database = & JFactory::getDBO();
		JHTML::_('stylesheet', 'icons.css', JURI::base().'components/com_biblestudy/css/');
		$mediaedit		=& $this->get('Data');
		$isNew		= ($mediaedit->id < 1);
		$lists = array();
		$admin = $this->get('Admin');
		$admin_params = new JParameter($admin[0]->params);
		$directory = ($admin_params->get('media_imagefolder') != '' ? '/images/'.$admin_params->get('media_imagefolder') : '/components/com_biblestudy/images');
		$javascript			= 'onchange="changeDisplayImage();"';
		$lists['media']	= JHTML::_('list.images',  'path2', $mediaedit->path2, $javascript, $directory, "bmp|gif|jpg|png|swf"  );
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $mediaedit->published);
		$text = $isNew ? JText::_( 'JBS_CMN_NEW' ) : JText::_( 'JBS_CMN_EDIT' );
		JToolBarHelper::title(   JText::_( 'JBS_MED_EDIT_MEDIA' ).': <small><small>[ ' . $text.' ]</small></small>', 'mediaimages.png' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		// Add an upload button and view a popup screen width 550 and height 400
		$alt = "Upload";
		$bar=& JToolBar::getInstance( 'toolbar' );
		$bar->appendButton( 'Popup', 'upload', $alt, "index.php?option=com_media&tmpl=component&task=popupUpload&directory=", 650, 400 );
		jimport( 'joomla.i18n.help' );
		JToolBarHelper::help( 'biblestudy', true );
		$this->assignRef('directory', $directory);
		$this->assignRef('mediaedit',		$mediaedit);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}
?>