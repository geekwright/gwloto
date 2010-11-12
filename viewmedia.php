<?php
/**
* viewmedia.php - display media item detail for viewing or editing
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

if(isset($currentmedia)) unset($currentmedia);
if(isset($_POST['mid'])) $currentmedia = intval($_POST['mid']);
else if(isset($_GET['mid'])) $currentmedia = intval($_GET['mid']);

if(isset($currentmedia)) {
	$sql="SELECT media_auth_place FROM ".$xoopsDB->prefix('gwloto_media');
	$sql.= " WHERE media_id = $currentmedia ";

	$result = $xoopsDB->query($sql);
	if($result) {
		$myrow=$xoopsDB->fetchArray($result);
		$currentplace=$myrow['media_auth_place'];
		$xoopsDB->freeRecordSet($result);
	}
}

if(!isset($currentmedia)) {
	$err_message=_MD_GWLOTO_MSG_BAD_PARMS;
//	redirect_header('index.php', 3, $err_message);
}

// check authorities
$user_can_edit=false;
$user_can_view=false;
if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT])) $user_can_edit=true;
if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_TRANS])) $user_can_edit=true;
if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_VIEW])) $user_can_view=true;
if($user_can_edit) $user_can_view=true;

// leave if we don't have any  authority
if(!$user_can_view) {
		$err_message = _MD_GWLOTO_MSG_NO_AUTHORITY;
		redirect_header('index.php', 3, $err_message);
}

// check for select mode on clipboard
	$caption='';
	$returnurl='';
	$attachextra='';
	$attach_type=$places['userinfo']['clipboard_type'];
	$generic_id=$places['userinfo']['clipboard_id'];
	$media_select_mode=false;

	switch($attach_type) {
		case 'mediaattach_place':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_PLACE,getPlaceName($generic_id, $language));
			$returnurl="index.php?pid=$generic_id";
			$attachextra="pid=$generic_id";
			break;
		case 'mediaattach_plan':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_PLAN,getCplanName($generic_id, $language));
			$returnurl="viewplan.php?cpid=$generic_id";
			$attachextra="cpid=$generic_id";
			break;
		case 'mediaattach_point':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_POINT,getCpointName($generic_id, $language));
			$returnurl="viewpoint.php?ptid=$generic_id";
			$attachextra="ptid=$generic_id";
			break;
		case 'mediaattach_job':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_JOB,getJobName($generic_id,$language));
			$returnurl="viewjob.php?jid=$generic_id";
			$attachextra="jid=$generic_id";
			break;
		case 'mediaattach_jobstep':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_JOBSTEP,getJobStepName($generic_id,$language));
			$returnurl="viewstep.php?jsid=$generic_id";
			$attachextra="jsid=$generic_id";
			break;
	}

	if($caption!='') {
		$xoopsTpl->assign('media_mode_select',$caption);
		$media_select_mode=true;
	}

// cancel button
	if(isset($_POST['media_cancel_button'])) {
		setClipboard($myuserid);
		if($returnurl==='') $returnurl='index.php';
		redirect_header($returnurl, 3,_MD_GWLOTO_MEDIA_SELECT_CANCELED);
	}
// attach button
	if(isset($_POST['media_select_button']) && isset($_POST['mid'])) {
		$cleanredir="attachmedia.php?mid=$currentmedia&$attachextra";
		$dirname=$xoopsModule->getInfo('dirname');
		header('Location: ' . XOOPS_URL . '/modules/'.$dirname.'/'. $cleanredir);
		exit;
	}

$op='display';
if(isset($_POST['delete'])) {
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT])) $op='delete';
	else {
		if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_TRANS])) {
			if($language==0) $err_message = _MD_GWLOTO_MSG_NO_TRANSLATE_DEFAULT;
			else  $op='deletetran';
		}
	}
	if($op=='delete' && $language!=0) $op='deletetran';
}
if(isset($_POST['update'])) {
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT])) $op='update';
	else {
		if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_TRANS])) {
			if($language==0) $err_message = _MD_GWLOTO_MSG_NO_TRANSLATE_DEFAULT;
			else  $op='updatetran';
		}
	}
}
// translator functionality
if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_TRANS])) {
	if(isset($_POST['lchange'])) {
		$op='lchange';
	}
}

if(!$user_can_edit) {
	$op='display';
}

if ($op!='display') {
	$check=$GLOBALS['xoopsSecurity']->check();

	if (!$check) {
		$op='display';
		$err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
	}
}


/*
CREATE TABLE gwloto_media (
  media_id int(8) unsigned NOT NULL auto_increment,
  media_class ENUM('permit','form','diagram','instructions','manual','MSDS','other') NOT NULL default 'other',
  media_fileref int(8) unsigned NOT NULL,
  media_auth_place int(8) unsigned NOT NULL,
  PRIMARY KEY (media_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_media_file (
  media_file_id int(8) unsigned NOT NULL auto_increment,
  media_filename varchar(255) NOT NULL,
  media_storedname varchar(255) NOT NULL,
  media_mimetype varchar(255) NOT NULL,
  media_size int unsigned NOT NULL default '0',
  last_uploaded_by int(8) NOT NULL,
  last_uploaded_on int(10) NOT NULL,
  PRIMARY KEY (media_file_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_media_detail (
  media int(8) unsigned NOT NULL,
  language_id tinyint unsigned NOT NULL default '0',
  media_name varchar(100) NOT NULL,
  media_description text NOT NULL,
  media_lang_fileref int(8) unsigned NOT NULL default '0',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (media, language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_media_attach (
  media_attach_id int(8) unsigned NOT NULL auto_increment,
  attach_type ENUM('place','plan','point','job','jobstep') NOT NULL,
  generic_id int(8) unsigned NOT NULL,
  media_id int(8) unsigned NOT NULL,
  media_order int(8) unsigned NOT NULL default '0',
  required tinyint unsigned NOT NULL default '0',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (media_attach_id),
  UNIQUE KEY (attach_type, generic_id, media_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

*/

$max_media_size=getMaxMediaSize();
// from gwloto_media
$media_class='';
$media_fileref=0;
$media_auth_place=0;
// from gwloto_media_detail
$media_name='';
$media_description='';
$media_lang_fileref=0;
$last_changed_by=0;
$last_changed_on=0;
// from gwloto_media_file
$media_file_id=0;
$media_filename='';
$media_storedname='';
$media_mimetype='';
$last_uploaded_by=0;
$last_uploaded_on=0;

$media_link='';
$uploaded_stored_file='';

// get data from tables

	$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_media').', '.$xoopsDB->prefix('gwloto_media_detail');
	$sql.= " WHERE media_id = $currentmedia AND (language_id=$language OR language_id=0) ";
	$sql.= ' AND media = media_id ';
	$sql.= ' ORDER BY language_id ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$media_class=$myrow['media_class'];
			$media_fileref=$myrow['media_fileref'];
			$media_auth_place=$myrow['media_auth_place'];
			$media_name=$myrow['media_name'];
			$media_description=$myrow['media_description'];
			$media_lang_fileref=$myrow['media_lang_fileref'];
			$last_changed_by=$myrow['last_changed_by'];
			$last_changed_on=$myrow['last_changed_on'];
			$language_id=$myrow['language_id'];
		}

		$media_file_id=$media_fileref;
		if($media_lang_fileref!=0) $media_file_id=$media_lang_fileref;

		$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_media_file').', '.$xoopsDB->prefix('gwloto_media_detail');
		$sql.= " WHERE media_file_id = $media_file_id ";

		$result = $xoopsDB->query($sql);
		if ($result) {
			while($myrow=$xoopsDB->fetchArray($result)) {
				$media_filename=$myrow['media_filename'];
				$media_mimetype=$myrow['media_mimetype'];
				$last_uploaded_by=$myrow['last_uploaded_by'];
				$last_uploaded_on=$myrow['last_uploaded_on'];
			}
		}

	}
	else {
		$err_message = _MD_GWLOTO_MEDIA_NOTFOUND;
		redirect_header('index.php', 3, $err_message);
	}
	$org_media_filename=$media_filename;

function zapMediaFile($media_file_id) {
global $xoopsDB;

	if($media_file_id) {
		$pathname=getMediaUploadPath();

		$sql='SELECT media_storedname  FROM '. $xoopsDB->prefix('gwloto_media_file');
		$sql.= " WHERE media_file_id = $media_file_id ";

		$result = $xoopsDB->query($sql);
		if ($result) {
			while($myrow=$xoopsDB->fetchArray($result)) {
				$filename=$myrow['media_storedname'];
				if($filename!='') unlink($pathname.$filename);
			}
			$sql='DELETE  FROM '. $xoopsDB->prefix('gwloto_media_file');
			$sql.= " WHERE media_file_id = $media_file_id ";

			$result = $xoopsDB->queryF($sql);
		}
	}
}

if ($op=='delete') {
// collect media_lang_fileref from gwloto_media_detail
// zap media_fileref and media_lang_fileref(s) 
		$sql='SELECT media_lang_fileref  FROM '. $xoopsDB->prefix('gwloto_media_detail');
		$sql.= " WHERE media = $currentmedia AND media_lang_fileref != 0 ";

		$result = $xoopsDB->query($sql);
		if ($result) {
			while($myrow=$xoopsDB->fetchArray($result)) {
				$fileref=$myrow['media_lang_fileref'];
				zapMediaFile($fileref);
			}
		}
		zapMediaFile($media_fileref);
// delete gwloto_media_attach by media_id
		$sql='DELETE  FROM '. $xoopsDB->prefix('gwloto_media_attach');
		$sql.= " WHERE media_id = $currentmedia ";
		$result = $xoopsDB->queryF($sql);
// delete gwloto_media_detail by media
		$sql='DELETE  FROM '. $xoopsDB->prefix('gwloto_media_detail');
		$sql.= " WHERE media = $currentmedia ";
		$result = $xoopsDB->queryF($sql);
// delete gwloto_media by media_id
		$sql='DELETE  FROM '. $xoopsDB->prefix('gwloto_media');
		$sql.= " WHERE media_id = $currentmedia ";
		$result = $xoopsDB->queryF($sql);

		$op='display';

		$message = _MD_GWLOTO_MEDIA_DELETE_OK;
		redirect_header("listmedia.php?pid=$currentplace", 3, $message);
}

if ($op=='deletetran') {
	if($language_id==$language) {
		zapMediaFile($media_lang_fileref);

		$sql='DELETE  FROM '. $xoopsDB->prefix('gwloto_media_detail');
		$sql.= " WHERE media = $currentmedia AND language_id = $language ";

		$result = $xoopsDB->queryF($sql);
		$message = _MD_GWLOTO_MEDIA_DELETE_OK;
		redirect_header("viewmedia.php?mid=$currentmedia", 3, $message);
	}
	else $err_message = _MD_GWLOTO_MEDIA_DELETE_DB_ERROR;
	$op='display';
}

// get form values
if($op!='lchange') {
	if($op!='updatetran') {
		// these don't change with translation
		if(isset($_POST['media_class'])) $media_class=cleaner($_POST['media_class']);
		if(isset($_POST['pid'])) $media_auth_place=intval($_POST['pid']);
	}

	if(isset($_POST['media_name'])) $media_name=cleaner($_POST['media_name']);
	if(isset($_POST['media_description'])) $media_description=cleaner($_POST['media_description']);
	if(isset($_POST['media_link'])) $media_link=cleaner($_POST['media_link']);
}
if($op=='lchange') $op='display';

if($op!='display') {
// check for an updated file or link
	$action_on_media_file='none';
	if(isset($_POST['xoops_upload_file'][0])) {
		$filekey=$_POST['xoops_upload_file'][0];
		if(isset($_FILES[$filekey]) && !$_FILES[$filekey]['error']) {
			$zapus = array(' ', '/', '\\');
			$filename = str_replace($zapus, '_', $_FILES[$filekey]['name']);

			$filename=uniqid().'_'.str_replace('.','_',$filename);
			$pathname=getMediaUploadPath();
			$uploaded_stored_file=$pathname.$filename;
			if (move_uploaded_file($_FILES[$filekey]['tmp_name'], $uploaded_stored_file)) {

				$media_file_id=0;
				$media_filename=cleaner($_FILES[$filekey]['name']);

				$media_storedname=$filename;
				$media_mimetype=cleaner($_FILES[$filekey]['type']);
				if($media_mimetype=='link') $media_mimetype='';
				$media_size=intval($_FILES[$filekey]['size']);
			}
			else {
				$err_message=_MD_GWLOTO_MEDIA_FILE_MOVE_ERROR;
				$op='display';
			}
			$media_link='';
			$action_on_media_file='replace';
			if($language!=$language_id || 
			  ($language!=0 && $media_lang_fileref==0)) {
				$action_on_media_file='addlanguage';
			}
			if($language==$language_id && $language!=0 && $media_lang_fileref!=0) {
				$action_on_media_file='replacelanguage';
			}
		}
		else {
			if($media_link!='') {
				$media_storedname='';
				$media_filename=$media_link;
				$media_mimetype='link';
				$media_size=0;
				$media_link_mode=true;
				$action_on_media_file='replace';
				if($language!=$language_id || 
				  ($language!=0 && $media_lang_fileref==0)) {
					$action_on_media_file='addlanguage';
				}
				if($language==$language_id && $language!=0 && $media_lang_fileref!=0) {
					$action_on_media_file='replacelanguage';
				}
			}
			else {
				$action_on_media_file='none';
			}
		}
	}
}

if($op!='display') {
// start transaction
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

// update gwloto_media_file as needed
	switch($action_on_media_file) {
		case 'replace':
			if(!$dberr) {
				$sql =' UPDATE '.$xoopsDB->prefix('gwloto_media_file');
				$sql.=" SET media_filename = '$sl_media_filename' ";
				$sql.=" , media_storedname = '$sl_media_storedname' ";
				$sql.=" , media_mimetype = '$sl_media_mimetype' ";
				$sql.=" , media_size = $media_size ";
				$sql.=" , last_uploaded_by = $myuserid ";
				$sql.=" , last_uploaded_on = UNIX_TIMESTAMP() ";
				$sql.=" WHERE media_file_id = $media_fileref ";
				$result = $xoopsDB->queryF($sql);
				if (!$result) {
					$dberr=true;
					$dbmsg=formatDBError();
				}
			}
			break;
		case 'addlanguage':
			$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_media_file');
			$sql.=' (media_filename, media_storedname, media_mimetype, media_size, last_uploaded_by, last_uploaded_on) ';
			$sql.=" VALUES ('$sl_media_filename', '$sl_media_storedname', '$sl_media_mimetype', $media_size,  $myuserid, UNIX_TIMESTAMP() )";
			$result = $xoopsDB->queryF($sql);
			if (!$result) {
				$dberr=true;
				$dbmsg=formatDBError();
			}
			else $media_lang_fileref = $xoopsDB->getInsertId();
			break;
		case 'replacelanguage':
			if(!$dberr) {
				$sql =' UPDATE '.$xoopsDB->prefix('gwloto_media_file');
				$sql.=" SET media_filename = '$sl_media_filename' ";
				$sql.=" , media_storedname = '$sl_media_storedname' ";
				$sql.=" , media_mimetype = '$sl_media_mimetype' ";
				$sql.=" , media_size = $media_size ";
				$sql.=" , last_uploaded_by = $myuserid ";
				$sql.=" , last_uploaded_on = UNIX_TIMESTAMP() ";
				$sql.=" WHERE media_file_id = $media_lang_fileref ";
				$result = $xoopsDB->queryF($sql);
				if (!$result) {
					$dberr=true;
					$dbmsg=formatDBError();
				}
			}
			break;
	}

// update gwloto_media_detail
	if(!$dberr) {

		$sql =' UPDATE '.$xoopsDB->prefix('gwloto_media_detail');
		$sql.=" SET media_name = '$sl_media_name' ";
		$sql.=" , media_description = '$sl_media_description' ";
		$sql.=" , media_lang_fileref = $media_lang_fileref ";
		$sql.=" , last_changed_by = $myuserid ";
		$sql.=" , last_changed_on = UNIX_TIMESTAMP() ";
		$sql.=" WHERE media = $currentmedia AND language_id = $language ";

		$result = $xoopsDB->queryF($sql);
		if ($result) {
			$rcnt=$xoopsDB->getAffectedRows();
			if($rcnt==0 && $language!=0) {
				$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_media_detail');
				$sql.=' (media, language_id, media_name, media_description, media_lang_fileref, last_changed_by, last_changed_on) ';
				$sql.=" VALUES ($currentmedia, $language, '$sl_media_name', '$sl_media_description', $media_lang_fileref,$myuserid, UNIX_TIMESTAMP() )";

				$result = $xoopsDB->queryF($sql);
			}
		}
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

// update gwloto_media
	if($op!='updatetran') {
		if(!$dberr) {
			$sql =' UPDATE '.$xoopsDB->prefix('gwloto_media');
			$sql.=" SET media_class = '$sl_media_class' ";
			$sql.=" , media_auth_place = $media_auth_place ";
			$sql.=" WHERE media_id = $currentmedia ";

			$result = $xoopsDB->queryF($sql);
			if (!$result) {
				$dberr=true;
				$dbmsg=formatDBError();
			}
		}
	}
// end transaction
	if(!$dberr) {
		commitTransaction();
		$message = _MD_GWLOTO_MEDIA_UPDATE_OK;
//		redirect_header("viewmedia.php?mid=$currentmedia", 3, $message);
	}
	else {
		if($uploaded_stored_file!='') unlink($uploaded_stored_file);
		rollbackTransaction();
		$err_message = _MD_GWLOTO_MEDIA_UPDATE_DB_ERROR .' '.$dbmsg;
	}
}

if($user_can_edit) {
	$token=true;

	$formtitle=_MD_GWLOTO_MEDIA_EDIT_FORM;

	$form = new XoopsThemeForm($formtitle, 'form1', 'viewmedia.php', 'POST', $token);
	$form->setExtra(' target="_self" enctype="multipart/form-data" ');


	$caption = _MD_GWLOTO_MEDIA_NAME;
	$form->addElement(new XoopsFormText($caption, 'media_name', 40, 90, htmlspecialchars($media_name, ENT_QUOTES)),true);

	$pids=getPlacesByUserAuth($myuserid,_GWLOTO_USERAUTH_MD_EDIT,$language);
	if(empty($pids)) {
		// if user has translate but not edit authority
		$form->addElement(new XoopsFormHidden('pid', $currentplace));
	}
	else {
		$caption = _MD_GWLOTO_MEDIA_AUTHPLACE;

		$placechoice=$media_auth_place;
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
	}
	$caption = _MD_GWLOTO_MEDIA_FILE_TO_UPLOAD;
	$form->addElement(new XoopsFormFile($caption, 'userfile', $max_media_size),false);

	$caption = _MD_GWLOTO_MEDIA_LINK;
	$form->addElement(new XoopsFormText($caption, 'media_link', 40, 255, htmlspecialchars($media_link, ENT_QUOTES)),false);

	$caption = _MD_GWLOTO_MEDIA_FILE_NAME;
	$form->addElement(new XoopsFormLabel($caption,'<a href="'."dlmedia.php?mid=$currentmedia&lid=$language".'" target="_blank">'.htmlspecialchars($org_media_filename, ENT_QUOTES).'</a>', 'media_filename'));

	$thisUser =& $member_handler->getUser($last_uploaded_by);
        if (!is_object($thisUser)) {
		$user_name=$last_uploaded_by;
	} else {
		$user_name = $thisUser->getVar('name');
		if($user_name=='') $user_name = $thisUser->getVar('uname');
	}
	$caption = _MD_GWLOTO_MEDIA_UPLOAD_BY;
	$form->addElement(new XoopsFormLabel($caption,$user_name, 'last_uploaded_by'),false);

	$caption = _MD_GWLOTO_MEDIA_UPLOAD_ON;
	$form->addElement(new XoopsFormLabel($caption,getDisplayDate($last_uploaded_on), 'last_uploaded_on'),false);

	$caption = _MD_GWLOTO_MEDIA_CLASS;
	$listboxmc = new XoopsFormSelect($caption, 'media_class', $media_class, 1, false);
	$listboxmc->addOption('',_MD_GWLOTO_MEDIA_CLASS_SELECT);
	foreach($mediaclass as $i=>$v) {
		$listboxmc->addOption($i, $v);
	}
	$form->addElement($listboxmc,true);

	$caption = _MD_GWLOTO_MEDIA_DESCRIPTION;
	$form->addElement(new XoopsFormTextArea($caption, 'media_description', $media_description, 10, 50, 'media_description'),true);

	$member_handler =& xoops_gethandler('member');

	$thisUser =& $member_handler->getUser($last_changed_by);
        if (!is_object($thisUser)) {
		$user_name=$last_changed_by;
	} else {
		$user_name = $thisUser->getVar('name');
		if($user_name=='') $user_name = $thisUser->getVar('uname');
	}
	$caption = _MD_GWLOTO_LASTCHG_BY;
	$form->addElement(new XoopsFormLabel($caption,$user_name, 'last_changed_by'),false);

	$caption = _MD_GWLOTO_LASTCHG_ON;
	$form->addElement(new XoopsFormLabel($caption,getDisplayDate($last_changed_on), 'last_changed_on'),false);

	$form->addElement(new XoopsFormHidden('mid', $currentmedia));

	// translator functionality
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_TRANS])) {
		$available_languages=getLanguages();

		$caption = _MD_GWLOTO_LANG_TRAY;

		$langtray=new XoopsFormElementTray($caption, '');

		$listbox = new XoopsFormSelect('', 'lid', $language, 1, false);
		foreach ($available_languages as $i => $v) {
			$listbox->addOption($i, $v);
		}
		$langtray->addElement($listbox);

		$langtray->addElement(new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_CHANGE_BUTTON, 'submit'));

		$googleTranslateEnabled=$xoopsModuleConfig['enable_google'];
		$langcodes=getLanguageCodes();
		foreach($langcodes as $i => $v) {
			if($v=='') $googleTranslateEnabled=false;
		}

		if($googleTranslateEnabled) {
			$xoTheme->addScript('http://www.google.com/jsapi');

			$translate_js = '';
			$langcodes=getLanguageCodes();
			$translate_js .= 'var langCodes=new Array();';
			foreach($langcodes as $i => $v) {
				$translate_js .= "langCodes[$i]=\"$v\";";
			}

			$translate_js .= <<<ENDJSCODE

google.load("language", "1");
google.setOnLoadCallback(googleinit);

function googleinit() {
	google.language.getBranding("googlebranding");
}

function doTranslate(form) {
	var langId = form.lid.value;

	var content = new Object;
	content.type = 'text';
	content.text = form.media_description.value;
	if(content.text.length>0) {
		google.language.translate(content, "", langCodes[langId], function(result) {
			if (!result.error) {
				form.media_description.value=result.translation;
			} } );
	}

	var content = new Object;
	content.type = 'text';
	content.text = form.media_name.value;
	if(content.text.length>0) {
		google.language.translate(content, "", langCodes[langId], function(result) {
			if (!result.error) {
				form.media_name.value=result.translation;
			} } );
	}
}
ENDJSCODE;

			$xoTheme->addScript( null, array( 'type' => 'text/javascript' ), $translate_js );

			$translate_button=new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_TRANS_BUTTON, 'button');
			$translate_button->setExtra(' onClick=\'doTranslate(this.form)\' ');
			$langtray->addElement($translate_button);
			$langtray->addElement(new XoopsFormLabel('', '<span id=\'googlebranding\'> </span>', 'branding'),false);
		}


		$form->addElement($langtray);
	}
	else {
		$form->addElement(new XoopsFormHidden('lid', $language_id));
	}

	$caption = _MD_GWLOTO_MEDIA_TOOL_TRAY_DSC;
	$mttray=new XoopsFormElementTray($caption, '');

	$mttray->addElement(new XoopsFormButton('', 'update', _MD_GWLOTO_MEDIA_SAVE_BUTTON, 'submit'));

	$mttraydel=new XoopsFormButton('', 'delete', _MD_GWLOTO_MEDIA_DELETE_BUTTON, 'submit');
	$mttraydel->setExtra('onClick=\'this.form.target = "_self";return confirm("'._MD_GWLOTO_DELETE_SEL_CONFIRM.'")\'');
	$mttray->addElement($mttraydel);
	if($media_select_mode) {
		$mttray->addElement(new XoopsFormButton('', 'media_select_button', _MD_GWLOTO_MEDIA_SELECT_BUTTON, 'submit'));
		$mttray->addElement(new XoopsFormButton('', 'media_cancel_button', _MD_GWLOTO_MEDIA_CANCEL_BUTTON, 'submit'));
	}
	$form->addElement($mttray);

	$body=$form->render();
}
else { // view only
	$myts = myTextSanitizer::getInstance();
	$media_description=$myts->nl2Br($media_description);

	$token=false;

	$formtitle=_MD_GWLOTO_MEDIA_VIEW_FORM;

	$form = new XoopsThemeForm($formtitle, 'form1', 'viewstep.php', 'POST', $token);

	$caption = _MD_GWLOTO_MEDIA_NAME;
	$form->addElement(new XoopsFormLabel($caption,'<a href="'."dlmedia.php?mid=$currentmedia&lid=$language".'" target="_blank">'.htmlspecialchars($media_name, ENT_QUOTES).'</a>', 'media_name'));

	$caption = _MD_GWLOTO_MEDIA_CLASS;
	$form->addElement(new XoopsFormLabel($caption, $mediaclass[$media_class], 'media_class'));

	$caption = _MD_GWLOTO_MEDIA_DESCRIPTION;
	$form->addElement(new XoopsFormLabel($caption, $media_description,'step_name'));

	$caption = _MD_GWLOTO_MEDIA_AUTHPLACE;
	$place_name=htmlspecialchars(getPlaceName($media_auth_place,$language), ENT_QUOTES);
	$form->addElement(new XoopsFormLabel($caption,$place_name, 'place_name'));

	$member_handler =& xoops_gethandler('member');

	$thisUser =& $member_handler->getUser($last_changed_by);
        if (!is_object($thisUser)) {
		$user_name=$last_changed_by;
	} else {
		$user_name = $thisUser->getVar('name');
		if($user_name=='') $user_name = $thisUser->getVar('uname');
	}
	$caption = _MD_GWLOTO_LASTCHG_BY;
	$form->addElement(new XoopsFormLabel($caption,$user_name, 'last_changed_by'),false);

	$caption = _MD_GWLOTO_LASTCHG_ON;
	$form->addElement(new XoopsFormLabel($caption,getDisplayDate($last_changed_on), 'last_changed_on'),false);

	$form->addElement(new XoopsFormHidden('mid', $currentmedia));
	$form->addElement(new XoopsFormHidden('pid', $currentplace));

	if($media_select_mode) {
		$caption = _MD_GWLOTO_MEDIA_TOOL_TRAY_DSC;
		$mttray=new XoopsFormElementTray($caption, '');

		$mttray->addElement(new XoopsFormButton('', 'media_select_button', _MD_GWLOTO_MEDIA_SELECT_BUTTON, 'submit'));
		$mttray->addElement(new XoopsFormButton('', 'media_cancel_button', _MD_GWLOTO_MEDIA_CANCEL_BUTTON, 'submit'));
		$form->addElement($mttray);
	}


	$body=$form->render();
} // end view only

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//if(isset($action_on_media_file)) $debug.='<pre>$action_on_media_file='.print_r($action_on_media_file,true).'</pre>';

if(isset($body)) $xoopsTpl->assign('body', $body);

setPageTitle(_MD_GWLOTO_TITLE_VIEWMEDIA);

$xoopsTpl->assign('crumburl','listmedia.php');
//$xoopsTpl->assign('crumbextra','&uid='.$auth_uid);
$xoopsTpl->assign('crumbpcurl','listmedia.php');

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
