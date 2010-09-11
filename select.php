<?php
/**
* select.php - clipboard operations move, copy, delete
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
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_index.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/userauth.php');
include ('include/userauthlist.php');
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');

function getCountAsParentPlace($pid) {
	global $xoopsDB;

	$cnt=0;

	$sql='SELECT count(*) as count FROM '. $xoopsDB->prefix('gwloto_place');
	$sql.= " WHERE parent_id = $pid ";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		$myrow=$xoopsDB->fetchArray($result);
		if($myrow) {
			$cnt=$myrow['count'];
		}
	}

	return $cnt;
}

function getCountOfPlacePlans($pid) {
	global $xoopsDB;

	$cnt=0;

	$sql='SELECT count(*) as count FROM '. $xoopsDB->prefix('gwloto_cplan');
	$sql.= " WHERE place_id = $pid ";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		$myrow=$xoopsDB->fetchArray($result);
		if($myrow) {
			$cnt=$myrow['count'];
		}
	}

	return $cnt;
}


function getParentPlace($pid) {
	global $xoopsDB;

	$ppid=0;

	$sql='SELECT parent_id FROM '. $xoopsDB->prefix('gwloto_place');
	$sql.= " WHERE place_id = $pid ";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		$myrow=$xoopsDB->fetchArray($result);
		if($myrow) {
			$ppid=$myrow['parent_id'];
		}
	}

	return $ppid;
}


function deletePlace($pid) {
	global $xoopsDB;
	global $err_message;


	$placecnt=getCountAsParentPlace($pid);
	$plancnt=getCountOfPlacePlans($pid);

	if($placecnt>0 || $plancnt>0) {
		$err_message=sprintf(_MD_GWLOTO_DELETE_SEL_PLACE_IN_USE,$placecnt,$plancnt);
		return false;
	}
	
/*	if(getParentPlace($pid)==0) {
		if(getCountAsParentPlace(0)==1) {
			$err_message=sprintf(_MD_GWLOTO_DELETE_SEL_ONLY_TOP_PLACE, $placecnt, $plancnt);
			return false;
		}
	}
*/
	$dberr=false;
	$dbmsg='';
	startTransaction();

	if(!$dberr) {
		$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_place_detail');
		$sql.= " WHERE place = $pid ";
	
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_place');
		$sql.= " WHERE place_id = $pid ";
	
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_user_auth');
		$sql.= " WHERE place_id = $pid ";
	
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_media_attach');
		$sql.= " WHERE attach_type = 'place' AND generic_id = $pid ";
	
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) commitTransaction();
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_DELETE_SEL_ERR .' '.$dbmsg;
	}
	return !$dberr;
}

function deletePlan($cpid) {
	global $xoopsDB;
	global $err_message;
	$dberr=false;
	$dbmsg='';
	startTransaction();

	$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_cpoint_detail');
	$sql.= ' WHERE cpoint IN ';
	$sql.= ' (select cpoint_id FROM '. $xoopsDB->prefix('gwloto_cpoint');
	$sql.= " WHERE cplan_id = $cpid )";
	
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$sql='DELETE FROM '.$xoopsDB->prefix('gwloto_cpoint');
		$sql.= " WHERE cplan_id = $cpid ";
	
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_cplan_detail');
		$sql.= " WHERE cplan = $cpid ";
	
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_cplan');
		$sql.= " WHERE cplan_id = $cpid ";
	
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_media_attach');
		$sql.= " WHERE attach_type = 'plan' AND generic_id = $cpid ";
	
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) commitTransaction();
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_DELETE_SEL_ERR .' '.$dbmsg;
	}
	return !$dberr;
}

function deletePoint($ptid) {
	global $xoopsDB;
	global $err_message;
	$dberr=false;
	$dbmsg='';
	startTransaction();

	$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_cpoint_detail');
	$sql.= " WHERE cpoint = $ptid ";
	
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_cpoint');
		$sql.= " WHERE cpoint_id = $ptid ";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		$sql='DELETE FROM '. $xoopsDB->prefix('gwloto_media_attach');
		$sql.= " WHERE attach_type = 'point' AND generic_id = $ptid ";
	
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) commitTransaction();
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_DELETE_SEL_ERR .' '.$dbmsg;
	}
	return !$dberr;
	

	return true;
}

function copyPlan($pid,$cpid,$uid) {
	global $xoopsDB;
	global $err_message,$myuserid;
	$dberr=false;
	$dbmsg='';
	startTransaction();

	$cpointcnt=getCPointCounts($cpid);
	$nextseq=$cpointcnt['count']+1;

	$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cplan');
	$sql.=' (place_id, last_changed_by, last_changed_on) ';
	$sql.=" VALUES($pid, $uid, UNIX_TIMESTAMP()) ";
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$new_plan_id = $xoopsDB->getInsertId();
		$copyof=_MD_GWLOTO_COPY_NAME_PREFIX;
		$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cplan_detail');
		$sql.=' (cplan, language_id, cplan_name, cplan_review, hazard_inventory, required_ppe, last_changed_by, last_changed_on) ';
		$sql.=" SELECT $new_plan_id, language_id, CONCAT('$copyof',cplan_name), cplan_review, hazard_inventory, required_ppe, $uid, UNIX_TIMESTAMP() ";
		$sql.=' FROM '.$xoopsDB->prefix('gwloto_cplan_detail');
		$sql.=" WHERE cplan = $cpid ";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		$cpoints=array();

		$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_cpoint');
		$sql.= " WHERE cplan_id = $cpid ";
	
		$result = $xoopsDB->query($sql);
		if ($result) {
			while($myrow=$xoopsDB->fetchArray($result)) {
				$cpoints[]=$myrow;
			}
		}
		else {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		foreach($cpoints as $i=>$r) {
			$seq_disconnect=$r['seq_disconnect'];
			$seq_reconnect=$r['seq_reconnect'];
			$seq_inspection=$r['seq_inspection'];
			$locks_required=$r['locks_required'];
			$tags_required=$r['tags_required'];

			$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cpoint');
			$sql.=' (cplan_id, seq_disconnect, seq_reconnect, seq_inspection, locks_required, tags_required) ';
			$sql.=" VALUES ($new_plan_id, $seq_disconnect, $seq_reconnect, $seq_inspection, $locks_required, $tags_required ) ";

			$result = $xoopsDB->queryF($sql);
			if ($result) {
				$cpoints[$i]['new_cpoint'] = $xoopsDB->getInsertId();
			}
			else {
				$dberr=true;
				$dbmsg=formatDBError();
				break;
			}
		}
	}

	if(!$dberr) {
		foreach($cpoints as $r) {
			$cpoint_id=$r['cpoint_id'];
			$new_cpoint=$r['new_cpoint'];
	
			$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cpoint_detail');
			$sql.=' (cpoint, language_id, cpoint_name, disconnect_instructions, disconnect_state, reconnect_instructions, reconnect_state, inspection_instructions, last_changed_by, last_changed_on) ';
			$sql.=" SELECT $new_cpoint, language_id, cpoint_name, disconnect_instructions, disconnect_state, reconnect_instructions, reconnect_state, inspection_instructions, $uid, UNIX_TIMESTAMP() ";
			$sql.=' FROM '.$xoopsDB->prefix('gwloto_cpoint_detail');
			$sql.=" WHERE cpoint = $cpoint_id ";

			$result = $xoopsDB->queryF($sql);
			if (!$result) {
				$dberr=true;
				$dbmsg=formatDBError();
				break;
			}
		}
	}

	if($dberr) {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_COPY_SEL_ERR .' '.$dbmsg;
		return false;
	}
	else {
		commitTransaction();
		return $new_plan_id;
	}
}

function copyPoint($cpid,$ptid,$uid) {
	global $xoopsDB;
	global $err_message,$myuserid;
	$dberr=false;
	$dbmsg='';
	startTransaction();

	$cpointcnt=getCPointCounts($cpid);
	$nextseq=$cpointcnt['count']+1;

	$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cpoint');
	$sql.=' (cplan_id, seq_disconnect, seq_reconnect, seq_inspection, locks_required, tags_required) ';
	$sql.=" SELECT $cpid, $nextseq, $nextseq, $nextseq, locks_required, tags_required ";
	$sql.=' FROM '.$xoopsDB->prefix('gwloto_cpoint');
	$sql.=" WHERE cpoint_id = $ptid ";
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$new_point_id = $xoopsDB->getInsertId();
		$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cpoint_detail');
		$sql.=' (cpoint, language_id, cpoint_name, disconnect_instructions, disconnect_state, reconnect_instructions, reconnect_state, inspection_instructions, last_changed_by, last_changed_on) ';
		$sql.=" SELECT $new_point_id, language_id, cpoint_name, disconnect_instructions, disconnect_state, reconnect_instructions, reconnect_state, inspection_instructions, $uid, UNIX_TIMESTAMP() ";
		$sql.=' FROM '.$xoopsDB->prefix('gwloto_cpoint_detail');
		$sql.=" WHERE cpoint = $ptid ";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if($dberr) {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_COPY_SEL_ERR .' '.$dbmsg;
		return false;
	}
	else {
		commitTransaction();
		return $new_point_id;
	}
}

function movePoint($cpid,$ptid,$uid) {
	global $xoopsDB;
	global $err_message;
	$dberr=false;
	$dbmsg='';
	startTransaction();

	$cpointcnt=getCPointCounts($cpid);
	$nextseq=$cpointcnt['count']+1;

	$sql ='UPDATE '.$xoopsDB->prefix('gwloto_cpoint');
	$sql.=" SET cplan_id = $cpid ";
	$sql.=", seq_disconnect=$nextseq, seq_reconnect=$nextseq, seq_inspection=$nextseq ";
	$sql.=" WHERE cpoint_id = $ptid";
	
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$sql ='UPDATE '.$xoopsDB->prefix('gwloto_cpoint_detail');
		$sql.=" SET last_changed_by = $uid ";
		$sql.="   , last_changed_on = UNIX_TIMESTAMP() ";
		$sql.=" WHERE cpoint = $ptid ";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) commitTransaction();
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_MOVE_SEL_ERR .' '.$dbmsg;
	}
	return !$dberr;
}

function movePlan($pid,$cpid,$uid) {
	global $xoopsDB;
	global $err_message;
	$dberr=false;
	$dbmsg='';
	startTransaction();

	$sql ='UPDATE '.$xoopsDB->prefix('gwloto_cplan');
	$sql.=" SET place_id = $pid ";
	$sql.=" , last_changed_by = $uid ";
	$sql.=" , last_changed_on = UNIX_TIMESTAMP() ";
	$sql.=" WHERE cplan_id = $cpid ";
	
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$sql ='UPDATE '.$xoopsDB->prefix('gwloto_cplan_detail');
		$sql.=" SET last_changed_by = $uid ";
		$sql.="   , last_changed_on = UNIX_TIMESTAMP() ";
		$sql.=" WHERE cplan = $cpid ";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) commitTransaction();
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_MOVE_SEL_ERR .' '.$dbmsg;
	}
	return !$dberr;
}

function movePlace($pid,$parent_pid,$uid) {
	global $xoopsDB;
	global $err_message;
	$dberr=false;
	$dbmsg='';
	startTransaction();

	// this shouldn't happen, but just in case
	if(getParentPlace($pid)==0) {
		if(getCountAsParentPlace(0)==1) {
			$err_message=sprintf(_MD_GWLOTO_DELETE_SEL_ONLY_TOP_PLACE, $placecnt, $plancnt);
			return false;
		}
	}

	$sql ='UPDATE '.$xoopsDB->prefix('gwloto_place');
	$sql.=" SET parent_id = $parent_pid ";
	$sql.=" WHERE place_id = $pid";
	
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$sql ='UPDATE '.$xoopsDB->prefix('gwloto_place_detail');
		$sql.=" SET last_changed_by = $uid ";
		$sql.="   , last_changed_on = UNIX_TIMESTAMP() ";
		$sql.=" WHERE place = $pid ";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) commitTransaction();
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_MOVE_SEL_ERR .' '.$dbmsg;
	}
	return !$dberr;
}

if(isset($_POST['op_cancel'])) {
	setClipboard($myuserid);
	$xoopsTpl->clear_assign('cbform');
	$message=_MD_GWLOTO_CANCEL_SEL_OK;
	if(isset($currentplan)) redirect_header("viewplan.php?cpid=$currentplan", 3, $message);
	if(isset($currentplace)) redirect_header("index.php?pid=$currentplace", 3, $message);
	redirect_header('index.php', 3, $message);
}

// leave if we don't have any edit authority
if(!(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_JB_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT]) )) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

if(isset($_GET['ptid']) || isset($_POST['ptid'])) {
	$caption = _MD_GWLOTO_EDITPOINT_NAME;
	$selname = getCpointName($currentpoint,$language);
	$idname='ptid';
	$idvalue=$currentpoint;
}
elseif(isset($_GET['cpid']) || isset($_POST['cpid'])) {
	$caption = _MD_GWLOTO_EDITPLAN_NAME;
	$selname = getCplanName($currentplan, $language);
	$idname='cpid';
	$idvalue=$currentplan;
}
elseif(isset($_GET['mid']) || isset($_POST['mid'])) {
	$caption = _MD_GWLOTO_MEDIA_NAME;
	$selname = getMediaName($currentmedia, $language);
	$idname='mid';
	$idvalue=$currentmedia;
}
elseif(isset($_GET['pid']) || isset($_POST['pid'])) {
	$caption = _MD_GWLOTO_EDITPLACE_NAME;
	$selname = getPlaceName($currentplace, $language);
	$idname='pid';
	$idvalue=$currentplace;
}

// get clipboard contents
$clipid=0;
$cliptype='';
unset($cb_ptid);
unset($cb_cpid);
unset($cb_pid);

if(isset($_POST['op_copy']) || isset($_POST['op_move'])) {
	if(isset($places['userinfo']['clipboard_id'])) $clipid=intval($places['userinfo']['clipboard_id']);
	if(isset($places['userinfo']['clipboard_type'])) $cliptype=$places['userinfo']['clipboard_type'];

	if($clipid==0) $cliptype='';

	switch($cliptype) {
		case 'POINT':
			$cb_ptid = $clipid;
			$idname='ptid';
			break;
		case 'PLAN':
			$cb_cpid = $clipid;
			$idname='cpid';
			break;
		case 'PLACE':
			$cb_pid = $clipid;
			$idname='pid';
			break;
	}
}

$op='display';

if(isset($_POST['op_delete'])) {
	switch($idname) {
		case 'pid':
			$op='del_place';
			break;
		case 'cpid':
			$op='del_plan';
			break;
		case 'ptid':
			$op='del_point';
			break;
		case 'mid':
			$op='del_media';
			break;
	}
}

if(isset($_POST['op_movecopy'])) {
	switch($idname) {
		case 'pid':
			$op='cb_place';
			break;
		case 'cpid':
			$op='cb_plan';
			break;
		case 'ptid':
			$op='cb_point';
			break;
	}
}

if(isset($_POST['op_move'])) {
	switch($idname) {
		case 'pid':
			$op='mv_place';
			break;
		case 'cpid':
			$op='mv_plan';
			break;
		case 'ptid':
			$op='mv_point';
			break;
	}
}

if(isset($_POST['op_copy'])) {
	switch($idname) {
		case 'pid':
			$op='cp_place';
			break;
		case 'cpid':
			$op='cp_plan';
			break;
		case 'ptid':
			$op='cp_point';
			break;
	}
}

if ($op!='display') {
	$check=$GLOBALS['xoopsSecurity']->check();

	if (!$check) {
		$op='display';
		$err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
	}
}

switch($op) {
	// delete operations
	case 'del_point':
		if(!isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
		}
		if(deletePoint($currentpoint)) {
			$message=_MD_GWLOTO_DELETE_SEL_OK;
			redirect_header("viewplan.php?cpid=$currentplan", 3, $message);
		}
		break;
	case 'del_plan':
		if(!isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
		}
		if(deletePlan($currentplan)) {
			$message=_MD_GWLOTO_DELETE_SEL_OK;
			redirect_header("index.php?pid=$currentplace", 3, $message);
		}
		break;
	case 'del_place':
		if(!isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT])) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
		}
		$redirplace=getParentPlace($currentplace);
		if(deletePlace($currentplace)) {
			$message=_MD_GWLOTO_DELETE_SEL_OK;
			redirect_header("index.php?pid=$redirplace", 3, $message);
		}
		break;
	// set clipboard operations
	case 'cb_place':
		setClipboard($myuserid,'PLACE',$currentplace);
		$message=_MD_GWLOTO_MOVECOPY_SEL_OK;
		redirect_header("index.php?pid=$currentplace", 3, $message);
		break;
	case 'cb_plan':
		setClipboard($myuserid,'PLAN',$currentplan);
		$message=_MD_GWLOTO_MOVECOPY_SEL_OK;
		redirect_header("index.php?pid=$currentplace", 3, $message);
		break;
	case 'cb_point':
		setClipboard($myuserid,'POINT',$currentpoint);
		$message=_MD_GWLOTO_MOVECOPY_SEL_OK;
		redirect_header("viewplan.php?cpid=$currentplan", 3, $message);
		break;
	// move operations
	case 'mv_point':
		if(!isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
		}
		if(!isset($currentplan) || !isset($cb_ptid)) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
		}
		if(movePoint($currentplan,$cb_ptid,$myuserid)) {
			setClipboard($myuserid);
			$message=_MD_GWLOTO_MOVE_SEL_OK;
			redirect_header("viewplan.php?cpid=$currentplan", 3, $message);
		}
		break;
	case 'mv_plan':
		if(!isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
		}
		if(!isset($currentplace) || !isset($cb_cpid)) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
		}
		if(movePlan($currentplace,$cb_cpid,$myuserid)) {
			setClipboard($myuserid);
			$message=_MD_GWLOTO_MOVE_SEL_OK;
			redirect_header("viewplan.php?cpid=$cb_cpid", 3, $message);
		}
		break;
	case 'mv_place':
		if(!isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT])) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
		}
		if(!isset($currentplace) || !isset($cb_pid)) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
		}
		if(isset($places['chainup'][$cb_pid])) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
		}
		if(movePlace($cb_pid,$currentplace,$myuserid)) {
			setClipboard($myuserid);
			$message=_MD_GWLOTO_MOVE_SEL_OK;
			redirect_header("index.php?pid=$cb_pid", 3, $message);
		}
		break;
	// copy clipboard operations
	case 'cp_point':
		if(!isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
		}
		if(!isset($currentplan) || !isset($cb_ptid)) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
		}
		if($newpoint=copyPoint($currentplan,$cb_ptid,$myuserid)) {
			setClipboard($myuserid);
			$message=_MD_GWLOTO_COPY_SEL_OK;
			redirect_header("editpoint.php?ptid=$newpoint", 3, $message);
		}
		break;
	case 'cp_plan':
		if(!isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
		}
		if(!isset($currentplace) || !isset($cb_cpid)) {
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
		}
		if($newplan=copyPlan($currentplace,$cb_cpid,$myuserid)) {
			setClipboard($myuserid);
			$message=_MD_GWLOTO_COPY_SEL_OK;
			redirect_header("editplan.php?cpid=$newplan", 3, $message);
		}
		break;
	// nops
	case 'display':
		break;
	default:
		$err_message="$op not yet implemented";
		break;
}

	$token=true;

	$form = new XoopsThemeForm(_MD_GWLOTO_CHOOSE_SELECTED, 'form1', 'select.php', 'POST', $token);

//	$caption = _MD_GWLOTO_EDITPLAN_NAME;
	$form->addElement(new XoopsFormLabel($caption, $selname, 'sel_name'),false);

// XoopsFormLabel( [string $caption = ""], [string $value = ""], [ $name = ""])
	$form->addElement(new XoopsFormHidden($idname, $idvalue));


	$button= new XoopsFormButton(_MD_GWLOTO_DELETE_SELECTED, 'op_delete', _MD_GWLOTO_DELETE_SEL_BUTTON, 'submit');
$button->setExtra('onClick="return confirm(\''._MD_GWLOTO_DELETE_SEL_CONFIRM.'\')"');

	$form->addElement($button);

if($idname!='mid') {
	$form->addElement(new XoopsFormButton(_MD_GWLOTO_MOVECOPY_SELECTED, 'op_movecopy', _MD_GWLOTO_MOVECOPY_SEL_BUTTON, 'submit'));
}

	$body=$form->render();

//$debug="op=$op <br />";
//$debug.='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

setPageTitle(_MD_GWLOTO_TITLE_SELECT);

if(isset($body)) $xoopsTpl->assign('body', $body);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
