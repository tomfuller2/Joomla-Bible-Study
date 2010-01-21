<?php
defined('_JEXEC') or die();

function getMediatable($params, $row, $admin_params)
{
jimport ('joomla.application.component.helper');
//dump ($admin_params, 'admin_params: ');
if (!$row->id) {return FALSE;}
    global $mainframe, $option;
	$database = & JFactory::getDBO();
		$path1 = JPATH_SITE.DS.'components'.DS.'com_biblestudy'.DS.'helpers'.DS;
		include_once($path1.'filesize.php');
		include_once($path1.'filepath.php');
		include_once($path1.'duration.php');
		include_once($path1.'image.php');
		include_once ($path1.'helper.php');
	$database->setQuery('SELECT * FROM #__bsms_admin WHERE id = 1');
	$database->query();
	$admin = $database->loadObjectList();
 
	$d_path1 = ($admin_params->get('media_imagefolder') ? 'images/'.$admin_params->get('media_imagefolder') : 'components/com_biblestudy/images');
	$d_image = ($admin[0]->download ? '/'.$admin[0]->download : '/download.png');
	$d_path = $d_path1.$d_image;
	$download_tmp = getImage($d_path);
    $download_image = $download_tmp->path;
	$query_media1 = 'SELECT #__bsms_mediafiles.*,'
    . ' #__bsms_servers.id AS ssid, #__bsms_servers.server_path AS spath,'
    . ' #__bsms_folders.id AS fid, #__bsms_folders.folderpath AS fpath,'
    . ' #__bsms_media.id AS mid, #__bsms_media.media_image_path AS impath, #__bsms_media.media_image_name AS imname, #__bsms_media.path2 AS path2,'
    . ' #__bsms_media.media_alttext AS malttext,'
    . ' #__bsms_mimetype.id AS mtid, #__bsms_mimetype.mimetext'
    . ' FROM #__bsms_mediafiles'
    . ' LEFT JOIN #__bsms_media ON (#__bsms_media.id = #__bsms_mediafiles.media_image)'
    . ' LEFT JOIN #__bsms_servers ON (#__bsms_servers.id = #__bsms_mediafiles.server)'
    . ' LEFT JOIN #__bsms_folders ON (#__bsms_folders.id = #__bsms_mediafiles.path)'
    . ' LEFT JOIN #__bsms_mimetype ON (#__bsms_mimetype.id = #__bsms_mediafiles.mime_type)'
    . ' WHERE #__bsms_mediafiles.study_id = '.$row->id.' AND #__bsms_mediafiles.published = 1 ORDER BY ordering ASC, #__bsms_mediafiles.mime_type ASC';
    $database->setQuery( $query_media1 );
    $media1 = $database->loadObjectList('id');
	$rows2 = count($media1);
	
	
	$compat_mode = $admin_params->get('compat_mode');
	if ($rows2 < 1) { $mediatable = null; return $mediatable; }
	
	
	$mediatable = '<table class="mediatable"><tbody><tr>';
	$row_count = 0;
	foreach ($media1 as $media) {
		
	$row_count = $row_count + 1;
	//Load the parameters
	$itemparams = new JParameter ($media->params);
	$Itemid = $params->get('detailstemplateid', 1);
	if (!$media->path2) { $i_path = $media->impath; }
	if ($media->path2 && !$admin_params->get('media_imagefolder')) { $i_path = 'components/com_biblestudy/images/'.$media->path2; }
	if ($media->path2 && $admin_params->get('media_imagefolder')) { $i_path = 'images/'.$admin_params->get('media_imagefolder').'/'.$media->path2;}
	$image = getImage($i_path);
	
	$mediatable .= '<td>';
	
	//todo - not sure how much of this is needed
	 $idfield = '#__bsms_mediafiles.id';
	  $filesize = getFilesize($media->size);
	  $duration = getDuration($params, $row); //This one IS needed
	  //dump ($duration, 'duration: ');
	  //dump ($params);
      $mimetype = $media->mimetext;
      $src = JURI::base().$image->path;
	  $height = $image->height;
	  $width = $image->width;
      $ispath = 0;
	  $mime = '';
	  $path1 = getFilepath($media->id, $idfield, $mime);
	  //dump ($media->id);
      //Get the type of player to use
      
      //Players: 0 = getDirect, 1 = getAVR, 2 = getInternal
      $playertype = 0;
      $continue = 0;
     //$avrtest = $itemparams->get('player'); dump($avrtest, 'avrtest: ');
     if ($media->internal_viewer == 1) //dump ($media->internal_viewer, 'internal_viewer: ');
      {
      	if (($params->get('useavr') == 1 || !$params->get('useavr')) || $itemparams->get('player') == 2)
      		{
      			$playertype = 1;
   				$continue = 1;
				$textfiles = preg_match("/pdf|txt|doc/", $fileextension); 
				if ($textfiles > 0)
				{
					$playertype = 0;
					$continue = 1; 
				}
				 	
     		}
      } 	 //dump ($continue, 'continue - after oldavr: ');
      //Check to see if we have set the media player from above, if so, we skip the next step
      if ($continue == 0)
      {
	      	if ($params->get('useavr') == 1 || $itemparams->get('player') == 2)
	      	{
	      		$fileextension = substr($media->filename, -3,3);
		  		$textfiles = preg_match("/pdf|txt|doc/", $fileextension); 
				if ($textfiles > 0)
				{
					$playertype = 0;
					$continue = 1;
				}
				else
				{
					$playertype = 1;
	      			$continue = 1;
	      		}
	      	}
      } //dump ($textfiles, 'continue: ');
      if ($continue == 0)
      {
      		if ($params->get('media_player') == 1 || $itemparams->get('player') == 1)
      		{
      			$ismp3 = substr($media->filename,-3,3);
      			if ($ismp3 == 'mp3')
      			{
      				$playertype = 2;
      				$continue = 1;
      			}
      		}
      }
      if ($continue == 0)
     	{
      		$playertype = 0;      	
      	}
      //dump ($playertype, 'playertype: ');
      switch ($playertype)
      {
      	case 0:
      	$media1_link = getDirect($media, $width, $height, $duration, $src, $path1, $filesize);
		break;
		
		case 1:
		$media1_link = getAVR($media, $width, $height, $src, $params, $image, $Itemid);
		break;
		
		case 2:
		$media1_link = getInternal($media, $width, $height, $src, $params, $image, $row_count, $path1);
		break;
      }

	  if ($media->docMan_id > 0)
	 	{
			$media1_link = getDocman($media, $width, $height, $src, $duration, $filesize);
		}
	if ($media->article_id > 0)
		{
			$media1_link = getArticle($media, $width, $height, $src);
		}
	if ($media->virtueMart_id > 0)
		{
			$media1_link = getVirtuemart($media, $width, $height, $src, $params);
		}
		
      
       /**
        * @desc: I hope to in the future load media files using this method
        */
       /*  echo ('<div class="inlinePlayer" id="media-'.$media->id.'"></div>');
        echo ('<a href="'.$path1.'" class="btnPlay" alt="'.$media->id.'">Play</a>');*/


       /*$abspath    = JPATH_SITE;
        require_once($abspath.'/components/com_biblestudy/classes/class.biblestudymediadisplay.php');
        $inputtype = 0;
        $media_display = new biblestudymediadisplay($row->id, $inputtype);
        $media_display->id = $row->id;
        $media_display->inputtype = 0;*/

       // Here is where we begin to build the mediatable variable
	
	 //Here we test to see if docMan or article is used
	 
	
	
	$mediatable .= $media1_link; 
		//Download icon
		
		$link_type = $media->link_type;
		if ($link_type > 0){ //$src = JURI::base().$download_image;
	   $width=$download_tmp->width;
	   $height=$download_tmp->height;
	   
	  // dump ($compat_mode, 'compat_mode: ');
      if($compat_mode == 0) {
       $mediatable .='<a href="index.php?option=com_biblestudy&id='.$media->id.'&view=studieslist&controller=studieslist&task=download">';
	   
      }else{
       $mediatable .='<a href="http://joomlabiblestudy.org/router.php?file='.$media->spath.$media->fpath.$media->filename.'&size='.$media->size.'">';
	   
      }
     
	$mediatable .= '<img src="'.$download_image.'" alt="'.JText::_('Download').'" height="'.$height.'" width="'.$width.'" title="'.JText::_('Download').'" />'.JText::_('</a>'); 
  
	  }
	
	
	$mediatable .= '</td>';
	
	} //end of foreach of media results
	
	$mediatable .= '</tr>';

if ($params->get('show_filesize') > 0 ) 
	{
		$mediatable .= '<tr>';
		foreach ($media1 as $media) {
			switch ($params->get('show_filesize'))
				{
					case 1:
						$filesize = getFilesize($media->size);
					break;
					case 2:
						$filesize = $media->comment;
					break;
					case 3:
						if ($media->comment ? $filesize = $media->comment : $filesize = getFilesize($media->size));
					break;
				}
			
				$mediatable .= '<td><span class="bsfilesize">'.$filesize.'</span></td>';
				 
		} //end second foreach
		$mediatable .= '</tr>';
	} // end of if show_filesize

	$mediatable .='</table>';
    return $mediatable;
}

function getDocman($media, $width, $height, $src, $duration, $filesize)
	{
		$docman = '<a href="index.php?option=com_docman&task=doc_download&gid='.$media->docMan_id.'"
		 title="'.$media->malttext.' - '.$media->comment.'" target="'.$media->special.'"><img src="'.$src
       .'" alt="'.$media->malttext.' '.$duration.' '.$filesize.'" width="'.$width
       .'" height="'.$height.'" border="0" /></a>';
		
		
	return $docman;
	}
	
function getArticle($media, $width, $height, $src)
	{
		$article = '<a href="index.php?option=com_content&view=article&id='.$media->article_id.'"
		 alt="'.$media->malttext.' - '.$media->comment.'" target="'.$media->special.'"><img src="'.$src.'" width="'.$width
       	.'" height="'.$height.'" border="0" /></a>';
		
	return $article;
	}
	
function getVirtuemart($media, $width, $height, $src, $params)
	{
		
		$vm = '<a href="index.php?option=com_virtuemart&page=shop.product_details&flypage='.$params->get('store_page', 'flypage.tpl').'&product_id='.$media->virtueMart_id.'"
		alt="'.$media->malttext.' - '.$media->comment.'" target="'.$media->special.'"><img src="'.$src.'" width="'.$width
       	.'" height="'.$height.'" border="0" /></a>';
		
	return $vm;
	}
	
function getAVR($media, $width, $height, $src, $params, $image, $Itemid)
	{
		//dump ($media);
       JPluginHelper::importPlugin('system', 'avreloaded');
	   
       $studyfile = $media->spath.$media->fpath.$media->filename;
       $mediacode = $media->mediacode;
       
       $isrealfile = substr($media->filename, -4, 1);
       $fileextension = substr($media->filename,-3,3);
       if ($mediacode == '')
	   	{
			$mediacode = '{'.$fileextension.'remote}-{/'.$fileextension.'remote}';
       	}
       $mediacode = str_replace("'",'"',$mediacode);
       $ispop = substr_count($mediacode, 'popup');
       if ($ispop < 1) 
	   	{
        	$bracketpos = strpos($mediacode,'}');
        	$mediacode = substr_replace($mediacode,' popup="true" ',$bracketpos,0);
		}
       
	   $isdivid = substr_count($mediacode, 'divid');
       if ($isdivid < 1) 
	   	{
        	$dividid = ' divid="'.$media->id.'"';
        	$bracketpos = strpos($mediacode, '}');
        	$dividid = $dividid.' Itemid="2"';
        	$mediacode = substr_replace($mediacode, $dividid,$bracketpos,0);
       	}
       $isonlydash = substr_count($mediacode, '}-{');
       if ($isonlydash == 1)
	   	{
        	$ishttp = substr_count($studyfile, 'http://');
        	if ($ishttp < 1) 
				{
         		$isrealfile = substr($media->filename, -4, 1);
         			if ($isrealfile == '.') 
						{
          					$isslash = substr_count($studyfile,'//');
          						if (!$isslash) 
									{
           								$studyfile = substr_replace($studyfile,'http://',0,0);
          							}
         				}
        		}
		
		
			if ($isrealfile != '.')
				{
				 $studyfile = $media->filename;
				}
			$mediacode = str_replace('-',$studyfile,$mediacode);
       }
       
	   $popuptype = 'window';
       if($params->get('popuptype') != 'window') 
	   	{
        	$popuptype = 'lightbox';
       	}
       
	  
		   $media1_link = $mediacode.'{avrpopup type="'.$popuptype.'" id="'.$media->id
       .'"}<img src="'.JURI::base().$image->path.'" alt="'.$media->malttext. ' - '.$media->comment
       .' '.$duration.' '.$filesize.'" width="'.$image->width
       .'" height="'.$image->height.'" border="0" title="'
       .$media->malttext.' - '.$media->comment.' '.$duration.' '.$filesize.'" />{/avrpopup}';	
     return $media1_link;	
	}
	
	function getInternal($media, $width, $height, $src, $params, $image, $row_count, $path1)
		{
			
			   $player_width = $params->get('player_width', 290);
			   $media1_link =
			 '<script language="JavaScript" src="'.JURI::base().'components/com_biblestudy/audio-player.js"></script>
		<object type="application/x-shockwave-flash" data="'.JURI::base().'components/com_biblestudy/player.swf" id="audioplayer'.$row_count.'" height="24" width="'.$params->get('player_width', 290).'">
		<param name="movie" value="'.JURI::base().'components/com_biblestudy/player.swf">
		<param name="FlashVars" value="playerID='.$row_count.'&amp;soundFile='.$path1.'">
		<param name="quality" value="high">
		<param name="menu" value="false">
		<param name="wmode" value="transparent">
		</object> ';
			
		return $media1_link;
		}

function getDirect($media, $width, $height, $duration, $src, $path1, $filesize)
	{
       $media1_link = '<a href="'.$path1.'" title="'.$media->malttext.' - '.$media->comment.' '.$duration.' '
       .$filesize.'" target="'.$media->special.'"><img src="'.$src
       .'" alt="'.$media->malttext.' - '.$media->comment.' - '.$duration.' '.$filesize.'" width="'.$width
       .'" height="'.$height.'" border="0" /></a>';
	   
	   return $media1_link;
	}
	
	function getMediaRows($study_id) {
    $query = 'SELECT #_bsms_mediafiles.*,'
       . ' #_bsms_servers.id AS ssid, #_bsms_servers.server_path AS spath,'
       . ' #_bsms_folders.id AS fid, #_bsms_folders.folderpath AS fpath,'
       . ' #_bsms_media.id AS mid, #_bsms_media.media_image_path AS impath, #_bsms_media.media_image_name AS imname, #_bsms_media.path2 AS path2,'
       . ' #_bsms_media.media_alttext AS malttext,'
       . ' #_bsms_mimetype.id AS mtid, #_bsms_mimetype.mimetext'
       . ' FROM #_bsms_mediafiles'
       . ' LEFT JOIN #_bsms_media ON (#_bsms_media.id = #_bsms_mediafiles.media_image)'
       . ' LEFT JOIN #_bsms_servers ON (#_bsms_servers.id = #_bsms_mediafiles.server)'
       . ' LEFT JOIN #_bsms_folders ON (#_bsms_folders.id = #_bsms_mediafiles.path)'
       . ' LEFT JOIN #_bsms_mimetype ON (#_bsms_mimetype.id = #_bsms_mediafiles.mime_type)'
       . ' WHERE #_bsms_mediafiles.study_id = '.$study_id.' AND #_bsms_mediafiles.published = 1 ORDER BY ordering ASC, #_bsms_mediafiles.mime_type ASC;';
        
    $database = & JFactory::getDBO();
    $database->setQuery( $query );
    $database->query();
    $mediaRows = $database->loadObjectList();
    return $mediaRows;
    }