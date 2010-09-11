<?php
/**
* dlmedia.php - present a media item as either a file download
* or a redirect if the item is a link
*
* This file is part of gwloto - geekwright lockout tagout
*
* @copyright  Copyright © 2010 geekwright, LLC. All rights reserved. 
* @license    gwloto/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwloto
* @version    $Id$
*/

include '../../mainfile.php';
//$GLOBALS['xoopsOption']['template_main'] = 'gwloto_index.html';
//include(XOOPS_ROOT_PATH.'/header.php');
include_once ('include/dbcommon.php');

$media_id=0;
$language=0;

if(isset($_POST['mid'])) $media_id = intval($_POST['mid']);
else if(isset($_GET['mid'])) $media_id = intval($_GET['mid']);
if(isset($_POST['lid'])) $language = intval($_POST['lid']);
else if(isset($_GET['lid'])) $language = intval($_GET['lid']);

if($media_id==0) {
	die(_MD_GWLOTO_MSG_BAD_PARMS);
}

$media_fileref=0;
$media_lang_fileref=0;

$media_file_id='';
$media_filename='';
$media_storedname='';
$media_mimetype='';
$media_size=0;

	$sql='SELECT language_id, media_fileref, media_lang_fileref  FROM '. $xoopsDB->prefix('gwloto_media').', '.$xoopsDB->prefix('gwloto_media_detail');
	$sql.= " WHERE media_id = $media_id AND (language_id=$language OR language_id=0) ";
	$sql.= ' AND media = media_id ';
	$sql.= ' ORDER BY language_id ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$media_fileref = $myrow['media_fileref'];
			$media_lang_fileref = $myrow['media_lang_fileref'];
		}
	}
	else {
		die(_MD_GWLOTO_MSG_BAD_PARMS);
	}

	$sql='SELECT *  FROM '. $xoopsDB->prefix('gwloto_media_file');
	$sql.= " WHERE (media_file_id = $media_fileref OR media_file_id = $media_lang_fileref) ";

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$media_filename=$myrow['media_filename'];
			$media_storedname=$myrow['media_storedname'];
			$media_mimetype=$myrow['media_mimetype'];
			$media_size=$myrow['media_size'];

			$media_fileref = $myrow['media_file_id'];
			if($media_lang_fileref == $myrow['media_file_id']) break;
		}
	}
	else {
		die(_MD_GWLOTO_MSG_BAD_PARMS);
	}

	if($media_mimetype=='link') {
		header('Location: ' . $media_filename);
		exit;
	}

	$pathname=getMediaUploadPath();
	$file=$pathname.$media_storedname;


	if (file_exists($file)) {
		if(substr($media_mimetype,1,6) == 'image/') header('Content-Type: '.$media_mimetype);
		else {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');

		header('Content-Disposition: attachment; filename="'.$media_filename.'"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		}

//		header('Content-Length: ' . $media_size);
		ob_clean();
		flush();
		readfile($file);

	}
	else {
		die(_MD_GWLOTO_MSG_BAD_PARMS);
	}

//include(XOOPS_ROOT_PATH.'/footer.php');
?>
