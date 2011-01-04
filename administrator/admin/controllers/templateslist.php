<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');
class biblestudyControllertemplateedit extends JController {

	function __construct() {
		parent::__construct();

		//register extra tasks
		$this->registerTask('add', 'edit');
	}

	function edit() {
		JRequest::setVar('view', 'templateedit');
		JRequest::setVar('layout', 'form');
		JRequest::setVar('hidemenu', 1);

		parent::display();
	}

	function save()	{
		$model = $this->getModel('templateedit');
		$data = JRequest::get('post');
		if ($model->store($post)) {
			$msg = JText::_( 'JBS_TPL_TEMPLATE_SAVED' );
		} else {
			$msg = JText::_( 'JBS_TPL_ERROR_SAVING_TEMPLATE' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_biblestudy&view=templateslist';
		$this->setRedirect($link, $msg);
	}

	function cancel(){
		$msg = JText::_( 'JBS_CMN_OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_biblestudy&view=templateslist', $msg );
	}
}
?>

