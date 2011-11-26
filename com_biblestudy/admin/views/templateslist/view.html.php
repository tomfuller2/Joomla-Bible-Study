<?php

/**
 * @version     $Id$
 * @package BibleStudy
 * @Copyright (C) 2007 - 2011 Joomla Bible Study Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.JoomlaBibleStudy.org
 **/


//No Direct Access
defined('_JEXEC') or die();
require_once (JPATH_ADMINISTRATOR  .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_biblestudy' .DIRECTORY_SEPARATOR. 'lib' .DIRECTORY_SEPARATOR. 'biblestudy.defines.php');
require_once (JPATH_ADMINISTRATOR  .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_biblestudy' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'biblestudy.php');
jimport('joomla.application.component.view');

class biblestudyViewtemplateslist extends JView {

    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null) {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->canDo = BibleStudyHelper::getActions('', 'templateedit');
        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {
        JToolBarHelper::title(JText::_('JBS_CMN_TEMPLATES'), 'templates.png');
        if ($this->canDo->get('core.create')) 
        { JToolBarHelper::addNew('templateedit.add'); }
        if ($this->canDo->get('core.edit')) 
        {JToolBarHelper::editList('templateedit.edit');}
        if ($this->canDo->get('core.edit.state')) {
        JToolBarHelper::divider();
        JToolBarHelper::publishList('templateslist.publish');
        JToolBarHelper::unpublishList('templateslist.unpublish');
        }
        if ($this->canDo->get('core.delete')) 
        {JToolBarHelper::trash('templateslist.trash');}
        if ($this->state->get('filter.published') == -2 && $this->canDo->get('core.delete')) {
        JToolBarHelper::deleteList('', 'templateslist.delete','JTOOLBAR_EMPTY_TRASH');}
    }

}
