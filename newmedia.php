<?php
/**
* newmedia.php - add a file or link as a media item
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
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_editmedia.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');

$xoopsTpl->assign('mediaclass',$mediaclass);  // for select list

// leave if we don't have media edit authority
if(!isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT])) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

$max_media_size=getMaxMediaSize();

$media_id=0;
$media_class='';
$media_name='';
$media_description='';
$media_link='';
$media_link_mode=false;

$op='display';
if(isset($_POST['submit'])) {
	$op='add';
}

if(isset($_POST['media_class'])) $media_class = cleaner($_POST['media_class']);
if(isset($_POST['media_name'])) $media_name = cleaner($_POST['media_name']);
if(isset($_POST['media_link'])) $media_link = cleaner($_POST['media_link']);
if(isset($_POST['media_description'])) $media_description = cleaner($_POST['media_description']);

if ($op!='display') {
	$check=$GLOBALS['xoopsSecurity']->check();

	if (!$check) {
		$op='display';
		$err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
	}
}

if($op=='add') {
	$pathname=getMediaUploadPath();
	if(isset($_POST['xoops_upload_file'][0])) {
		$filekey=$_POST['xoops_upload_file'][0];
		if(isset($_FILES[$filekey]) && !$_FILES[$filekey]['error']) {
			$zapus = array(' ', '/', '\\');
			$filename = str_replace($zapus, '_', $_FILES[$filekey]['name']);

			$filename=uniqid().'_'.$filename;
			if (move_uploaded_file($_FILES[$filekey]['tmp_name'], $pathname.$filename)) {

				$media_file_id=0;
				$media_filename=cleaner($_FILES[$filekey]['name']);

				$media_storedname=$filename;
				$media_mimetype=cleaner($_FILES[$filekey]['type']);
				if($media_mimetype=='link') $media_mimetype='';
				$media_size=intval($_FILES[$filekey]['size']);
			}
			else {
				$err_message=_MD_GWLOTO_MEDIA_FILE_MOVE_ERROR;
				$op=$display;
			}
			$media_link='';
		}
		else {
			if($media_link!='') {
				$media_storedname='';
				$media_filename=$media_link;
				$media_mimetype='link';
				$media_size=0;
				$media_link_mode=true;
			}
			else {
				if($_FILES[$filekey]['error']==4) $err_message=_MD_GWLOTO_MEDIA_FILE_NOT_GIVEN;
				else $err_message=sprintf(_MD_GWLOTO_MEDIA_FILE_UPLOAD_ERROR,$_FILES[$filekey]['error']);
				$op=$display;
			}
		}
	}

}

if($op=='add') {
	$myts = myTextSanitizer::getInstance();
	$sl_media_filename=$myts->addslashes($media_filename);
	$sl_media_storedname=$myts->addslashes($media_storedname);
	$sl_media_mimetype=$myts->addslashes($media_mimetype);
	$sl_media_class=$myts->addslashes($media_class);
	$sl_media_name=$myts->addslashes($media_name);
	$sl_media_description=$myts->addslashes($media_description);

	$dberr=false;
	$dbmsg='';
	startTransaction();

	$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_media_file');
	$sql.=' (media_filename, media_storedname, media_mimetype, media_size, last_uploaded_by, last_uploaded_on) ';
	$sql.=" VALUES ('$sl_media_filename', '$sl_media_storedname', '$sl_media_mimetype', $media_size,  $myuserid, UNIX_TIMESTAMP() )";
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$media_fileref = $xoopsDB->getInsertId();
		$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_media');
		$sql.=' (media_class, media_fileref, media_auth_place) ';
		$sql.=" VALUES ('$sl_media_class', $media_fileref, $currentplace )";
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	  if(!$dberr) {
		$media_id = $xoopsDB->getInsertId();
		$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_media_detail');
		$sql.=' (media, media_name, media_description, last_changed_by, last_changed_on) ';
		$sql.=" VALUES ($media_id, '$sl_media_name', '$sl_media_description', $myuserid, UNIX_TIMESTAMP() )";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		commitTransaction();
		$message = _MD_GWLOTO_MEDIA_ADD_OK;
		redirect_header("viewmedia.php?mid=$media_id", 3, $message);
	}
	else {
		if(!$media_link_mode) unlink($pathname.$filename);
		rollbackTransaction();
		$err_message = _MD_GWLOTO_MEDIA_ADD_DB_ERROR .' '.$dbmsg;
	}
}

	$token=true;

	$caption = _MD_GWLOTO_MEDIA_ADD_FORM;
	$form = new XoopsThemeForm($caption, 'form1', 'newmedia.php', 'POST', $token);
	$form->setExtra(' enctype="multipart/form-data" ');

	$caption = _MD_GWLOTO_MEDIA_NAME;
	$form->addElement(new XoopsFormText($caption, 'media_name', 40, 90, htmlspecialchars($media_name, ENT_QUOTES)),true);

	$pids=getPlacesByUserAuth($myuserid,_GWLOTO_USERAUTH_MD_EDIT,$language);
	$caption = _MD_GWLOTO_MEDIA_AUTHPLACE;

	$placechoice=$currentplace;
	if(!isset($pids[$currentplace])) {
		$placechoice=false;
	}
	$listboxua = new XoopsFormSelect($caption, 'pid', $placechoice, 1, false);
	if(count($pids)>1) {
		$listboxua->addOption('',_MD_GWLOTO_MEDIA_AUTHPLACE_CHOOSE);
	}
	foreach($pids as $i=>$v) {
		$listboxua->addOption($i, $v);
	}
	$form->addElement($listboxua,true);


	$caption = _MD_GWLOTO_MEDIA_FILE_TO_UPLOAD;
	$form->addElement(new XoopsFormFile($caption, 'userfile', $max_media_size),false);

	$caption = _MD_GWLOTO_MEDIA_LINK;
	$form->addElement(new XoopsFormText($caption, 'media_link', 40, 255, htmlspecialchars($media_link, ENT_QUOTES)),false);

	$caption = _MD_GWLOTO_MEDIA_CLASS;
	$listboxmc = new XoopsFormSelect($caption, 'media_class', $media_class, 1, false);
	$listboxmc->addOption('',_MD_GWLOTO_MEDIA_CLASS_SELECT);
	foreach($mediaclass as $i=>$v) {
		$listboxmc->addOption($i, $v);
	}
	$form->addElement($listboxmc,true);

	$caption = _MD_GWLOTO_MEDIA_DESCRIPTION;
	$form->addElement(new XoopsFormTextArea($caption, 'media_description', $media_description, 10, 50, 'media_description'),true);

	$caption = _MD_GWLOTO_MEDIA_ADD_BUTTON_DSC;
	$form->addElement(new XoopsFormButton($caption, 'submit', _MD_GWLOTO_MEDIA_ADD_BUTTON, 'submit'));

	$body=$form->render();



//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$_FILES='.print_r($_FILES,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//$debug.='<pre>$pathname='.print_r($pathname,true).'</pre>';
//$debug.='<pre>$filename='.print_r($filename,true).'</pre>';
//$debug.='<pre>$pids='.print_r($pids,true).'</pre>';

setPageTitle(_MD_GWLOTO_TITLE_NEWMEDIA);

$xoopsTpl->assign('crumburl','');
//$xoopsTpl->assign('crumbextra','&uid='.$auth_uid);
$xoopsTpl->assign('crumbpcurl','');

if(isset($actions)) $xoopsTpl->assign('actions', $actions);
if(isset($body)) $xoopsTpl->assign('body', $body);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
