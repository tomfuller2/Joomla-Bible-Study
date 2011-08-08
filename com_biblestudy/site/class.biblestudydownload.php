<?php defined('_JEXEC') or die('Restricted access');
/**
 * @version $Id: class.biblestudydownload.php 1 $
 * @package BibleStudy
 * @Copyright (C) 2007 - 2011 Joomla Bible Study Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.JoomlaBibleStudy.org
 **/

class Dump_File{
  var $pathname = NULL;
  var $filename = NULL;
	var $filesieze = NULL;
	
  function download(){
 	
  	$id = JRequest::getVar('id', 0, 'GET', 'INT');
  	$hits = $this->hitDownloads($id);
	$template = JRequest::getInt('t','1','get');	
	$db	= & JFactory::getDBO();
    //Get the template so we can find a protocol
    $query = 'SELECT id, params FROM #__bsms_templates WHERE `id` = '.$template;
    $db->setQuery($query);
    $db->query();
    $template = $db->loadObject();
 //   $params = new JParameter($template->params); 
    
    // Convert parameter fields to objects.
				$registry = new JRegistry;
				$registry->loadJSON($template->params);
                $params = $registry;
                
    $protocol = $params->get('protocol','http://');
	$query = 'SELECT #__bsms_mediafiles.*,'
		. ' #__bsms_servers.id AS ssid, #__bsms_servers.server_path AS spath,'
		. ' #__bsms_folders.id AS fid, #__bsms_folders.folderpath AS fpath,'
		. ' #__bsms_mimetype.id AS mtid, #__bsms_mimetype.mimetext'
		. ' FROM #__bsms_mediafiles'
		. ' LEFT JOIN #__bsms_servers ON (#__bsms_servers.id = #__bsms_mediafiles.server)'
		. ' LEFT JOIN #__bsms_folders ON (#__bsms_folders.id = #__bsms_mediafiles.path)'
		. ' LEFT JOIN #__bsms_mimetype ON (#__bsms_mimetype.id = #__bsms_mediafiles.mime_type)'
		. ' WHERE #__bsms_mediafiles.id = '.$id.' LIMIT 1';
		$db->setQuery( $query );
		
	$media = $db->LoadObject();
	
	$server = $media->spath;
	$path = $media->fpath;
	$filename = $media->filename;
	$size = $media->size;
	$download_file = $protocol.$server.$path.$filename;
	$mime_type = $media->mimetext;
    $user_agent = (isset($_SERVER["HTTP_USER_AGENT"]) ) ? $_SERVER["HTTP_USER_AGENT"] : $HTTP_USER_AGENT;
    while (@ob_end_clean());
	$full = $server.$path.$filename;
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".basename($download_file));
    header("Content-Type: application/mp3");
    header("Content-Transfer-Encoding: binary");
    
	/**
	 * Set the "Content-Length" only if the filesize is above 0 byes
	 */
    if($size > 0) {
    	header("Content-Length: ".$size);
    }
    readfile($download_file);
	$url = $download_file;
	$out_file_name = $filename;
	//start
	}
function auto_download($url,$out_file_name){
	if( function_exists("curl_init") )
        return curl_download($url,$out_file_name);
	else
        return normal_download($url,$out_file_name);
}

// PHP Funtion : curl_download uses PHP curl module to download the file.
// It takes two parameter 1) Remote file url and 2) Local file name
function curl_download($url,$out_file_name){
	ini_set('memory_limit', '1000M');

	$out = fopen($out_file_name,"wb");
	
	if($out){
		$ch = curl_init(); 
		
		curl_setopt($ch, CURLOPT_FILE, $out); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		
		curl_exec($ch); 
		if(curl_error ($ch)){
			echo "<br>Error in downloading file. Curl error says : ".curl_error ($ch); 
		}
		else{
			echo "<br>File Download Success."; 
		}
		
		curl_close($ch);
	}else{
		echo "Error : Set Permissions 777 to the current directory";
	}
	fclose($out);
}

// PHP Funtion: normal_download  uses file_get_contents PHP function to download the file.
// It takes two parameter 1) Remote file url and 2) Local file name
function normal_download($url,$out_file_name){
	ini_set('memory_limit', '1000M');

	$out = fopen($out_file_name,"wb");
	
	if($out){
		
		fwrite($out,file_get_contents($url));
		
		if(curl_error ($ch)){
			echo "<br>Error in downloading file. Curl error says : ".curl_error ($ch); 
		}
		else{
			echo "<br>File Download Success."; 
		}
		
		
	}else{
		echo "Error : Set Permissions 777 to the current directory";
	}
	
	fclose($out);
}	

	//Here we increment the hit counter
 	function hitDownloads($id)
	{
		$db =& JFactory::getDBO();
		$db->setQuery('UPDATE '.$db->nameQuote('#__bsms_mediafiles').'SET '.$db->nameQuote('downloads').' = '.$db->nameQuote('downloads').' + 1 '.' WHERE id = '.$id);
		$db->query();
		return true;
	}
} //end of class