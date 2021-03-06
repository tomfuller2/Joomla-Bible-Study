<?php
/**
 * Part of Joomla BibleStudy Package
 *
 * @package    BibleStudy.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 */
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Thumbnail helper class
 *
 * @package  BibleStudy.Admin
 * @since    9.0.0
 */
class JBSMThumbnail
{

	/**
	 * Creates a thumbnail for an uploaded image
	 *
	 * @param   string  $file  File name
	 * @param   array   $path  Path to file
	 * @param   int     $size  Size of image with default of 100
	 *
	 * @return null
	 *
	 * @since 9.0.0
	 */
	public static function create($file, $path, $size = 100)
	{
		$name = basename($file);
		$original = JPATH_ROOT . '/' . $file;
		$thumb    = JPATH_ROOT . '/' . $path . '/thumb_' . $name;

		// Delete destination folder if it exists
		if (JFolder::exists(JPATH_ROOT . '/' . $path))
		{
			JFolder::delete(JPATH_ROOT . '/' . $path);
		}

		// Move uploaded image to destination
		JFolder::create(JPATH_ROOT . '/' . $path);

		// Create thumbnail
		$image     = new JImage($original);
		$thumbnail = $image->resize($size, $size, true);
		$thumbnail->toFile($thumb, IMAGETYPE_JPEG);
	}

	/**
	 * Resize image
	 *
	 * @param   string  $path      Path to file
	 * @param   int     $new_size  New image size
	 *
	 * @return null
	 */
	public static function resize($path, $new_size)
	{
		$filename = str_replace('original_', '', basename($path));

		// Delete existing thumbnail
		$old_thumbs = JFolder::files(dirname($path), 'thumb_', true, true);
		foreach ($old_thumbs as $thumb)
		{
			JFile::delete($thumb);
		}

		// Create new thumbnail
		$image     = new JImage($path);
		$thumbnail = $image->resize($new_size, $new_size);
		$thumbnail->toFile(dirname($path) . '/thumb_' . $filename, IMAGETYPE_PNG);
	}

	/**
	 * Resize image
	 *
	 * @param   string  $path  Path to file
	 * @param   string  $file  file to check
	 *
	 * @return null
	 */
	public static function check($path, $file = null)
	{
		if (!JFolder::exists($path))
		{
			return false;
		}
		elseif ($file)
		{
			return JFile::exists(JPATH_ROOT . $path . $file);
		}
		return true;
	}
}
