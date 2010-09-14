<?php
/**
* viewstep.php - display job step detail for view or edit
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
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_viewjob.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/userauth.php');
include ('include/userauthlist.php');
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');
include ('include/jobstatus.php');

// don't display clipboard
if(isset($_POST['op_addstep']) || isset($_POST['op_cancel'])) {
	setClipboard($myuserid);
	$xoopsTpl->clear_assign('cbform');
	if(isset($_POST['op_cancel'])) {
		redirect_header("viewjob.php?jid=$currentjob", 3, _MD_GWLOTO_CANCEL_SEL_OK);
	}
}

if(!isset($currentjob) || !isset($currentplan)) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
}

// since a job can have multiple places, the 'currentplace' might not be
// where the authority comes from, so we check the full job step places
// if we don't get authorities the normal way.
$user_can_edit=false;
$user_can_view=false;
if(isset($places['currentauth'][_GWLOTO_USERAUTH_JB_EDIT])) $user_can_edit=true;
if(isset($places['currentauth'][_GWLOTO_USERAUTH_JB_VIEW])) $user_can_view=true;
if(!$user_can_edit) $user_can_edit=checkJobAuthority($currentjob,$myuserid,true);
if($user_can_edit) $user_can_view=true;
if(!$user_can_view) $user_can_view=checkJobAuthority($currentjob,$myuserid,false);
// leave if we don't have any  authority
if(!$user_can_view) {
		$err_message = _MD_GWLOTO_MSG_NO_AUTHORITY;
		redirect_header('index.php', 3, $err_message);
}

$op='display';
if(isset($_POST['update'])) {
	$op='update';
}
if(isset($_POST['op_addstep']) || isset($_POST['add'])) {
	$jobcplans=getJobCplanIds($currentjob);
	if(!isset($jobcplans[$currentplan])) $op='add';
	else $err_message=_MD_GWLOTO_JOBSTEP_DUPLICATE_PLAN;
}
if(isset($_POST['print'])) {
	$prtmsg = _MD_GWLOTO_JOB_PRINT_REDIR_MSG;
	if($currentplan) redirect_header("printjob.php?jid=$currentjob&cpid=$currentplan", 3, $prtmsg);
	else redirect_header("printjob.php?jid=$currentjob", 3, $prtmsg);
}
if(!$user_can_edit) {
	$op='display';
}

$step_name='';
$assigned_uid=$myuserid;
$job_step_status='planning';
$last_changed_by=$myuserid;
$last_changed_on=0;
// get data from table

	$sql='SELECT * FROM '.$xoopsDB->prefix('gwloto_job_steps');
	$sql.=" WHERE job = $currentjob";
	$sql.=" AND cplan = $currentplan ";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$currentjobstep=$myrow['job_step_id'];
			$step_name=$myrow['step_name'];
			$assigned_uid=$myrow['assigned_uid'];
			$job_step_status = $myrow['job_step_status'];
			$display_job_step_status=$stepstatus[$job_step_status];
			$last_changed_by=$myrow['last_changed_by'];
			$last_changed_on=$myrow['last_changed_on'];
		}
	}
	else {
		if($op!='add') redirect_header('index.php', 3, _MD_GWLOTO_JOB_NOTFOUND);
	}

if($op=='update' || $op=='add') {
	if(isset($_POST['step_name'])) $step_name = cleaner($_POST['step_name']);
	if(isset($_POST['assigned_uid'])) $assigned_uid = intval($_POST['assigned_uid']);
	if(isset($_POST['job_step_status'])) $job_step_status = cleaner($_POST['job_step_status']);
	if(!isset($stepstatus[$job_step_status])) $job_step_status = 'planning';

	$myts = myTextSanitizer::getInstance();
	$sl_step_name=$myts->addslashes($step_name);

	$check=$GLOBALS['xoopsSecurity']->check();

	if (!$check) {
		$op='display';
		$err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
	}
}

if($op=='update') {
	$dberr=false;
	$dbmsg='';
	startTransaction();

	$sql ="UPDATE ".$xoopsDB->prefix('gwloto_job_steps');
	$sql.=" SET step_name = '$sl_step_name'";
	$sql.=" , assigned_uid = $assigned_uid ";
	$sql.=" , job_step_status = '$job_step_status' ";
	$sql.=" , last_changed_by = $myuserid ";
	$sql.=" , last_changed_on = UNIX_TIMESTAMP() ";
	$sql.=" WHERE job = $currentjob ";
	$sql.=" AND cplan = $currentplan ";

	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$rcnt=$xoopsDB->getAffectedRows();
	}

	if(!$dberr) {
		if($job_step_status!='planning' && $job_step_status!='canceled' && $job_step_status!='complete') {
			$sql ="UPDATE ".$xoopsDB->prefix('gwloto_job');
			$sql.=" SET job_status = 'active' ";
			$sql.=" WHERE job_id = $currentjob ";
			$result = $xoopsDB->queryF($sql);
		}
	}

	if(!$dberr) {
		commitTransaction();
		if($rcnt>0) {
			$message = _MD_GWLOTO_JOBSTEP_EDIT_OK;
			redirect_header("viewstep.php?jid=$currentjob&cpid=$currentplan", 3, $message);
		}
		else $op='add';
	}
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_JOBSTEP_EDIT_DB_ERROR .' '.$dbmsg;
		$op='display';
	}
}

if($op=='add') {
	$dberr=false;
	$dbmsg='';
	startTransaction();

	$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_job_steps');
	$sql.=' (job, cplan, step_name, assigned_uid, job_step_status, last_changed_by, last_changed_on) ';
	$sql.=" VALUES ($currentjob, $currentplan, '$sl_step_name', $assigned_uid, '$job_step_status', $myuserid, UNIX_TIMESTAMP() )";
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		foreach($places['chaindown'] as $v) {
			$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_job_places');
			$sql.=' (job, cplan, place) ';
			$sql.=" VALUES ($currentjob, $currentplan, $v)";

			$result = $xoopsDB->queryF($sql);
			if (!$result) {
				$dberr=true;
				$dbmsg=formatDBError();
				break;
			}
		}
	}

	if(!$dberr) {
		if($job_step_status!='planning' && $job_step_status!='canceled' && $job_step_status!='complete') {
			$sql ="UPDATE ".$xoopsDB->prefix('gwloto_job');
			$sql.=" SET job_status = 'active' ";
			$sql.=" WHERE job_id = $currentjob ";
			$result = $xoopsDB->queryF($sql);
		}
	}

	if(!$dberr) {
		commitTransaction();
		$message = _MD_GWLOTO_JOBSTEP_ADD_OK;
		redirect_header("viewstep.php?jid=$currentjob&cpid=$currentplan", 3, $message);
	}
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_JOBSTEP_ADD_DB_ERROR .' '.$dbmsg;
	}
	$op='display';
}

if($user_can_edit) {
	$jobreqs=getJobReqs();
	$token=true;

	if($op=='add') $formtitle=_MD_GWLOTO_JOBSTEP_NEW_FORM;
	else $formtitle=_MD_GWLOTO_JOBSTEP_EDIT_FORM;

	$form = new XoopsThemeForm($formtitle, 'form1', 'viewstep.php', 'POST', $token);

	$caption = _MD_GWLOTO_JOB_NAME;
	$job_name=htmlspecialchars(getJobName($currentjob,$language), ENT_QUOTES);
	$form->addElement(new XoopsFormLabel($caption,'<a href="viewjob?jid='.$currentjob.'">'.$job_name.'</a>', 'job_name'));

	$caption = _MD_GWLOTO_JOBSTEP_PLAN;
	$cplan_name=htmlspecialchars(getCplanName($currentplan, $language), ENT_QUOTES);
	$form->addElement(new XoopsFormLabel($caption, '<a href="viewplan.php?cpid='.$currentplan.'">'.$cplan_name.'</a>', 'cplan_name'),false);

	$caption = _MD_GWLOTO_JOBSTEP_STATUS;
	$listboxjs = new XoopsFormSelect($caption, 'job_step_status', $job_step_status, 1, false);
	foreach($stepstatus as $i=>$v) {
		$listboxjs->addOption($i, $v);
	}
	$form->addElement($listboxjs);

	$caption = _MD_GWLOTO_JOBSTEP_NAME;
	$form->addElement(new XoopsFormText($caption, 'step_name', 40, 250, htmlspecialchars($step_name, ENT_QUOTES)),$jobreqs['stepname']);

	$caption = _MD_GWLOTO_JOBSTEP_ASSIGNED_UID;
	$uid_choices=getUsersByAuth(_GWLOTO_USERAUTH_JB_EDIT,$places['chaindown'],false);
	$listboxau = new XoopsFormSelect($caption, 'assigned_uid', $assigned_uid, 1, false);
	foreach($uid_choices as $i=>$v) {
		$listboxau->addOption($i, $v);
	}
	$form->addElement($listboxau,true);

	if($op!='add') {
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
	}

	$form->addElement(new XoopsFormHidden('jid', $currentjob));
	$form->addElement(new XoopsFormHidden('cpid', $currentplan));

	$caption = _MD_GWLOTO_JOBSTEP_TOOL_TRAY_DSC;
	$jttray=new XoopsFormElementTray($caption, '');
	if($op=='add') {
		$jttray->addElement(new XoopsFormButton('', 'add', _MD_GWLOTO_JOBSTEP_ADD_BUTTON, 'submit'));
	} else {
		$jttray->addElement(new XoopsFormButton('', 'update', _MD_GWLOTO_JOBSTEP_EDIT_BUTTON, 'submit'));
		$jttray->addElement(new XoopsFormButton('', 'print', _MD_GWLOTO_JOB_PRINT_BUTTON, 'submit'));
	}
	$form->addElement($jttray);

	$body=$form->render();
}
else { // view only
	$token=true;

	$formtitle=_MD_GWLOTO_JOBSTEP_VIEW_FORM;

	$form = new XoopsThemeForm($formtitle, 'form1', 'viewstep.php', 'POST', $token);

	$caption = _MD_GWLOTO_JOB_NAME;
	$job_name=htmlspecialchars(getJobName($currentjob,$language), ENT_QUOTES);
	$form->addElement(new XoopsFormLabel($caption,'<a href="viewjob.php?jid='.$currentjob.'">'.$job_name.'</a>', 'job_name'));

	$caption = _MD_GWLOTO_JOBSTEP_PLAN;
	$cplan_name=htmlspecialchars(getCplanName($currentplan, $language), ENT_QUOTES);
	$form->addElement(new XoopsFormLabel($caption, '<a href="viewplan.php?cpid='.$currentplan.'">'.$cplan_name.'</a>', 'cplan_name'),false);

	$caption = _MD_GWLOTO_JOBSTEP_STATUS;
	$form->addElement(new XoopsFormLabel($caption, $display_job_step_status, 'job_step_status'));

	$caption = _MD_GWLOTO_JOBSTEP_NAME;
	$form->addElement(new XoopsFormLabel($caption, htmlspecialchars($step_name, ENT_QUOTES),'step_name'));

	$member_handler =& xoops_gethandler('member');

	$caption = _MD_GWLOTO_JOBSTEP_ASSIGNED_UID;
	$thisUser =& $member_handler->getUser($assigned_uid);
        if (!is_object($thisUser)) {
		$user_name=$assigned_uid;
	} else {
		$user_name = $thisUser->getVar('name');
		if($user_name=='') $user_name = $thisUser->getVar('uname');
	}
	$form->addElement(new XoopsFormLabel($caption,$user_name, 'assigned_uid'));

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

	$form->addElement(new XoopsFormHidden('jid', $currentjob));
	$form->addElement(new XoopsFormHidden('cpid', $currentplan));

	$caption = _MD_GWLOTO_JOBSTEP_TOOL_TRAY_DSC;
	$jttray=new XoopsFormElementTray($caption, '');
	$jttray->addElement(new XoopsFormButton('', 'print', _MD_GWLOTO_JOB_PRINT_BUTTON, 'submit'));
	$form->addElement($jttray);

	$body=$form->render();
}

//getAttachedMedia('jobstep', $currentjobstep, $language, $user_can_edit);
getAttachedMedia('plan', $currentplan, $language, false);

$steps=getJobSteps($currentjob);
if(isset($steps)) $xoopsTpl->assign('steps', $steps);

$jobs=getAvailableJobs($myuserid);
if(isset($jobs)) $xoopsTpl->assign('jobs',$jobs);

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//$debug.='<pre>$jobs='.print_r($jobs,true).'</pre>';
//$debug.='<pre>$steps='.print_r($steps,true).'</pre>';

if(isset($body)) $xoopsTpl->assign('body', $body);

setPageTitle(_MD_GWLOTO_TITLE_VIEWSTEP);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
