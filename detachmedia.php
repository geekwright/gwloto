<?php
/**
* detachmedia.php - detach media item from entity
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
// must have media_attach_id
if(!isset($_POST['maid'])) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
}

// get parms to establish environment from media_attach_id
	$media_attach_id=intval($_POST['maid']);
	$attach_type='';
	$generic_id=0;

	$sql ="SELECT attach_type, generic_id ";
	$sql.=' FROM '.$xoopsDB->prefix('gwloto_media_attach');
	$sql.=" WHERE media_attach_id = $media_attach_id ";

	$result = $xoopsDB->query($sql);

	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$attach_type=$myrow['attach_type'];
			$generic_id=$myrow['generic_id'];
		}
	}

	switch($attach_type) {
		case 'place':
			$_GET['pid']=$generic_id;
			break;
		case 'plan':
			$_GET['cpid']=$generic_id;
			break;
		case 'point':
			$_GET['ptid']=$generic_id;
			break;
		case 'job':
			$_GET['jid']=$generic_id;
			break;
		case 'jobstep':
			$_GET['jsid']=$generic_id;
			break;
		default:
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
			break;
	}
// now use the normal routines to establish the environment
include ('include/placeenv.php');

// now check that we have edit auth for the item that media is attached to
	$parmtest=false;

	switch($attach_type) {
		case 'place':
			$returnurl="editplace.php?pid=$generic_id";
			if(isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT])) $parmtest=true;
			break;
		case 'plan':
			$returnurl="editplan.php?cpid=$generic_id";
			if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) $parmtest=true;
			break;
		case 'point':
			$returnurl="editpoint.php?ptid=$generic_id";
			if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) $parmtest=true;
			break;
		case 'job':
			$returnurl="viewjob.php?jid=$generic_id";
			if(checkJobAuthority($generic_id,$myuserid,true)) $parmtest=true;
			break;
		case 'jobstep':
			$returnurl="viewstep.php?jsid=$generic_id";
			if(checkJobAuthority($currentjob,$myuserid,true)) $parmtest=true;
			break;
	}

// invalid parameters
	if(!isset($_POST['media_detach_button']) || $parmtest==false) {
		if($returnurl=='') $returnurl='index.php';
		redirect_header($returnurl, 3,_MD_GWLOTO_MEDIA_SELECT_CANCELED);
	}

	$sql ='DELETE FROM '.$xoopsDB->prefix('gwloto_media_attach');
	$sql.=" WHERE media_attach_id = $media_attach_id ";
	$result = $xoopsDB->queryF($sql);
	if ($result) {
		if($returnurl=='') $returnurl='index.php';
		redirect_header($returnurl, 3,_MD_GWLOTO_MEDIA_DETACH_OK);
	}
	else {
		$dbmsg=formatDBError();
		$err_message=_MD_GWLOTO_MEDIA_DETACH_DB_ERROR.' '.$dbmsg;
		if($returnurl=='') $returnurl='index.php';
		redirect_header($returnurl, 3,$err_message);
	}

?>
