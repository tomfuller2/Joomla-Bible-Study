<?php

/**
 * @version $Id: comment.php 2025 2011-08-28 04:08:06Z genu $
 * @package BibleStudy
 * @Copyright (C) 2007 - 2011 Joomla Bible Study Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.JoomlaBibleStudy.org
 **/

//No Direct Access
defined('_JEXEC') or die;



class TableComment extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	/**
	 * @var string
	 */
	var $study_id = null;
	var $user_id = null;
	var $comment_date = null;
	var $full_name = null;
	var $published = 1;
	var $comment_text = null;
	var $user_email = null;


	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableComment(& $db) {
		parent::__construct('#__bsms_comments', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}


		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules'])) {
			$rules = new JRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return      string
	 * @since       1.6
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_biblestudy.commentsedit.'.(int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return      string
	 * @since       1.6
	 */
	protected function _getAssetTitle()
	{
		$title = 'JBS Comment - id/date: '.$this->user_id.' - '.$this->comment_date;
		return $title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return      int
	 * @since       1.6
	 */
	protected function _getAssetParentId($table=null, $id=null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_biblestudy');
		return $asset->id;
	}
}