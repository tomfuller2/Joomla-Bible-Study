<?php
/**
 * Part of Joomla BibleStudy Package
 *
 * @package    BibleStudy.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
// No Direct Access
defined('_JEXEC') or die;

/**
 * Controller For MediaFile
 *
 * @package  BibleStudy.Admin
 * @since    7.0.0
 */
class BiblestudyControllerMediafile extends JControllerForm
{
	/**
	 * NOTE: This is needed to prevent Joomla 1.6's pluralization mechanisim from kicking in
	 *
	 * @since 7.0
	 */
	protected $view_list = 'mediafiles';

	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $option = 'com_biblestudy';

	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since    7.0.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method to add a new record.
	 *
	 * @return  mixed  True if the record can be added, a error object if not.
	 *
	 * @since   12.2
	 */
	public function add()
	{
		$app = JFactory::getApplication();

		if (parent::add())
		{
			$app->setUserState('com_biblestudy.edit.mediafile.createdate', null);
			$app->setUserState('com_biblestudy.edit.mediafile.study_id', null);
			$app->setUserState('com_biblestudy.edit.mediafile.server_id', null);

			return true;
		}

		return false;
	}

	/**
	 * Resets the User state for the server type. Needed to allow the value from the DB to be used
	 *
	 * @param   int     $key     ?
	 * @param   string  $urlVar  ?
	 *
	 * @return  bool
	 *
	 * @since   9.0.0
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app    = JFactory::getApplication();
		$result = parent::edit();

		if ($result)
		{
			$app->setUserState('com_biblestudy.edit.mediafile.createdate', null);
			$app->setUserState('com_biblestudy.edit.mediafile.study_id', null);
			$app->setUserState('com_biblestudy.edit.mediafile.server_id', null);
		}

		return true;
	}

	/**
	 * Handles XHR requests (i.e. File uploads)
	 *
	 * @return void
	 *
	 * @throws  Exception
	 * @since   9.0.0
	 */
	public function xhr()
	{
		JSession::checkToken('get') or die('Invalid Token');
		$input = JFactory::getApplication()->input;

		$addonType = $input->get('type', 'Legacy', 'string');
		$handler   = $input->get('handler');

		// Load the addon
		$addon = JBSMAddon::getInstance($addonType);

		if (method_exists($addon, $handler))
		{
			echo json_encode($addon->$handler($input));

			$app = JFactory::getApplication();
			$app->close();
		}
		else
		{
			throw new Exception(JText::sprintf('Handler: "' . $handler . '" does not exist!'), 404);
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   BiblestudyModelMediafile  $model  The model.
	 *
	 * @return  boolean     True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.6
	 */
	public function batch($model = null)
	{
		$model = $this->getModel('Mediafile', 'BiblestudyModel', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_biblestudy&view=mediafiles' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   12.2
	 */
	public function cancel($key = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app     = JFactory::getApplication();
		$model   = $this->getModel();
		$table   = $model->getTable();
		$checkin = property_exists($table, 'checked_out');

		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		$recordId = $app->input->getInt($key);

		// Attempt to check-in the current record.
		if ($recordId)
		{
			if ($checkin)
			{
				if ($model->checkin($recordId) === false)
				{
					// Check-in failed, go back to the record and display a notice.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
					$this->setMessage($this->getError(), 'error');

					$this->setRedirect(
						JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, $key), false
						)
					);

					return false;
				}
			}
		}

		if ($this->input->getCmd('return') && parent::cancel($key))
		{
			$this->setRedirect(base64_decode($this->input->getCmd('return')));
			return true;
		}
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
		return false;
	}

	/**
	 * Sets the server for this media record
	 *
	 * @return  void
	 *
	 * @since   9.0.0
	 */
	public function setServer()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$input = $app->input;

		$data = $input->get('jform', array(), 'post', 'array');
		$cdate = $data['createdate'];
		$study_id = $data['study_id'];
		$server_id = $data['server_id'];

		// Save server in the session
		$app->setUserState('com_biblestudy.edit.mediafile.createdate', $cdate);
		$app->setUserState('com_biblestudy.edit.mediafile.study_id', $study_id);
		$app->setUserState('com_biblestudy.edit.mediafile.server_id', $server_id);

		$redirect = $this->getRedirectToItemAppend($data['id']);
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $redirect, false));
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return    void
	 *
	 * @since    3.1
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$return = $this->input->getCmd('return');
		$task   = $this->input->get('task');
		if ($return && $task != 'apply')
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JBS_MED_SAVE'), 'message');
			$this->setRedirect(base64_decode($return));
		}
		return;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   12.2
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$tmpl    = $this->input->get('tmpl');
		$layout  = $this->input->get('layout', 'edit', 'string');
		$return  = $this->input->getCmd('return');
		$options = $this->input->get('options');
		$append = '';

		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout)
		{
			$append .= '&layout=' . $layout;
		}

		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		if ($options)
		{
			$append .= '&options=' . $options;
		}

		if ($return)
		{
			$append .= '&return=' . $return;
		}

		return $append;
	}
}
