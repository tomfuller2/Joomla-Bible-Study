<?php
/**
 * Part of Joomla BibleStudy Package
 *
 * @package    BibleStudy.Admin
 * @copyright  (C) 2007 - 2014 Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
// No Direct Access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Podcast form class
 *
 * @package  BibleStudy.Admin
 * @since    7.0.0
 */
class BiblestudyControllerPodcast extends JControllerForm
{

	/**
	 * Class constructor.
	 *
	 * @param   array $config  A named array of configuration variables.
	 *
	 * @since    7.0.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

}
