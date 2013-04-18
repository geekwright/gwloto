<?php
/**
* viewjob.php - display job detail for view or edit
*
* This file is part of gwloto - geekwright lockout tagout
*
* @copyright  Copyright © 2010-2011 geekwright, LLC. All rights reserved. 
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

$local_js = <<<ENDJSCODE
function html_entity_decode(str) {
 try {
  var tarea=document.createElement('textarea');
  tarea.innerHTML = str;
  return tarea.value;
  var decodedStr = tarea.value;
  document.removeElement(tarea);
  return decodedStr;
 }
 catch(e) {
	return str;
 }
}
ENDJSCODE;

$xoTheme->addScript( null, array( 'type' => 'text/javascript' ), $local_js );

if(!isset($currentjob)) {
	redirect_header('index.php', 3, _MD_GWLOTO_JOB_NOTFOUND);
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
if(isset($_POST['addstep'])) {
	$op='addstep';
}
if(isset($_POST['print'])) {
	$currentplan=false;
	if(isset($_POST['cpid'])) $currentplan = intval($_POST['cpid']);
	else if(isset($_GET['cpid'])) $currentplan = intval($_GET['cpid']);

	$prtmsg = _MD_GWLOTO_JOB_PRINT_REDIR_MSG;
	if($currentplan) redirect_header("printjob.php?jid=$currentjob&cpid=$currentplan", 3, $prtmsg);
	else redirect_header("printjob.php?jid=$currentjob", 3, $prtmsg);
}
if(!$user_can_edit) {
	$op='display';
}

$job_name='';
$job_workorder='';
$job_supervisor='';
$job_startdate='';
$job_enddate='';
$job_description='';
$job_status='';

// get data from table

	$sql='SELECT * FROM '.$xoopsDB->prefix('gwloto_job');
	$sql.=" WHERE job_id = $currentjob";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$job_name=$myrow['job_name'];
			$job_workorder=$myrow['job_workorder'];
			$job_supervisor=$myrow['job_supervisor'];
			$job_startdate = $myrow['job_startdate'];
			$job_enddate = $myrow['job_enddate'];
			$job_description = $myrow['job_description'];
			$job_status = $myrow['job_status'];
			$display_job_status=$jobstatus[$job_status];
			$last_changed_by=$myrow['last_changed_by'];
			$last_changed_on=$myrow['last_changed_on'];
		}
	}
	else {
		redirect_header('index.php', 3, _MD_GWLOTO_JOB_NOTFOUND);
	}

if($op=='addstep') {
	setClipboard($myuserid,'JOB',$currentjob);
	redirect_header("index.php?pid=$currentplace", 3, _MD_GWLOTO_JOBSTEP_ADD_PICK_MSG);
}

if($op=='update') {
	if(isset($_POST['job_name'])) $job_name = cleaner($_POST['job_name']);
	if(isset($_POST['job_workorder'])) $job_workorder = cleaner($_POST['job_workorder']);
	if(isset($_POST['job_supervisor'])) $job_supervisor = cleaner($_POST['job_supervisor']);
	if(isset($_POST['job_startdate'])) $job_startdate = cleaner($_POST['job_startdate']);
	if(isset($_POST['job_enddate'])) $job_enddate = cleaner($_POST['job_enddate']);
	if(isset($_POST['job_description'])) $job_description = cleaner($_POST['job_description']);
	if(isset($_POST['job_status'])) $job_status = cleaner($_POST['job_status']);

	$check=$GLOBALS['xoopsSecurity']->check();

	if (!$check) {
		$op='display';
		$err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
	}
}

if($op=='update') {
	$sl_job_name=dbescape($job_name);
	$sl_job_workorder=dbescape($job_workorder);
	$sl_job_supervisor=dbescape($job_supervisor);
	$sl_job_startdate=dbescape($job_startdate);
	$sl_job_enddate=dbescape($job_enddate);
	$sl_job_description=dbescape($job_description);

	if(isset($jobstatus[$job_status])) $db_job_status=$job_status;
	else $db_job_status='planning';

	$dberr=false;
	$dbmsg='';
	startTransaction();

	$sql ="UPDATE ".$xoopsDB->prefix('gwloto_job');
	$sql.=" SET job_name = '$sl_job_name'";
	$sql.=" , job_workorder = '$sl_job_workorder' ";
	$sql.=" , job_supervisor = '$sl_job_supervisor' ";
	$sql.=" , job_startdate = '$sl_job_startdate' ";
	$sql.=" , job_enddate = '$sl_job_enddate' ";
	$sql.=" , job_description = '$sl_job_description' ";
	$sql.=" , job_status = '$db_job_status' ";
	$sql.=" , last_changed_by = $myuserid ";
	$sql.=" , last_changed_on = UNIX_TIMESTAMP() ";
	$sql.=" WHERE job_id = $currentjob ";

	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		commitTransaction();
		$message = _MD_GWLOTO_JOB_EDIT_OK;
		redirect_header("viewjob.php?jid=$currentjob", 3, $message);
	}
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_JOB_EDIT_DB_ERROR .' '.$dbmsg;
	}
}

if($user_can_edit) {
	$jobreqs=getJobReqs();
	$token=true;

	$formtitle=_MD_GWLOTO_JOB_EDIT_FORM;

	$form = new XoopsThemeForm($formtitle, 'form1', 'viewjob.php', 'POST', $token);

	$caption = _MD_GWLOTO_JOB_NAME;
	$form->addElement(new XoopsFormText($caption, 'job_name', 40, 250, htmlspecialchars($job_name, ENT_QUOTES)),true);

	$caption = _MD_GWLOTO_JOB_STATUS;
	$listboxjs = new XoopsFormSelect($caption, 'job_status', $job_status, 1, false);
	foreach($jobstatus as $i=>$v) {
		$listboxjs->addOption($i, $v);
	}
	$form->addElement($listboxjs);

	$caption = _MD_GWLOTO_JOB_WORKORDER;
	$form->addElement(new XoopsFormText($caption, 'job_workorder', 40, 250, htmlspecialchars($job_workorder, ENT_QUOTES)),$jobreqs['workorder']);

	$caption = _MD_GWLOTO_JOB_SUPERVISOR;
	$svtray=new XoopsFormElementTray($caption, '<br />');
	$svtray->addElement(new XoopsFormText('', 'job_supervisor', 40, 80, htmlspecialchars($job_supervisor, ENT_QUOTES)),$jobreqs['supervisor']);
	$uid_choices=getUsersByAuth(_GWLOTO_USERAUTH_PL_SUPER,null,$currentjob);
	$listboxsv = new XoopsFormSelect('', 'pick_supervisor', $myuserid, 1, false);
	$listboxsv->addOption('', _MD_GWLOTO_JOB_PICKSUPER);
	foreach($uid_choices as $i) {
		$listboxsv->addOption($i, $i);
	}
	$listboxsv->setExtra('onChange="this.form.elements[\'job_supervisor\'].value = html_entity_decode(this.form.elements[\'pick_supervisor\'].value) " ');
	$svtray->addElement($listboxsv);
	$form->addElement($svtray);

	$caption = _MD_GWLOTO_JOB_STARTDATE;
	$form->addElement(new XoopsFormText($caption, 'job_startdate', 12, 30, htmlspecialchars($job_startdate, ENT_QUOTES)),$jobreqs['startdate']);

	$caption = _MD_GWLOTO_JOB_ENDDATE;
	$form->addElement(new XoopsFormText($caption, 'job_enddate', 12, 30, htmlspecialchars($job_enddate, ENT_QUOTES)),$jobreqs['enddate']);

	$caption = _MD_GWLOTO_JOB_DESCRIPTION;
	$form->addElement(new XoopsFormTextArea($caption, 'job_description', $job_description, 10, 50, 'job_description'),$jobreqs['description']);

	$member_handler =& xoops_gethandler('member');
	$thisUser =& $member_handler->getUser($last_changed_by);
        if (!is_object($thisUser) || !$thisUser->isActive() ) {
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

	$caption = _MD_GWLOTO_JOB_TOOL_TRAY_DSC;
	$jttray=new XoopsFormElementTray($caption, '');
	$jttray->addElement(new XoopsFormButton('', 'update', _MD_GWLOTO_JOB_EDIT_BUTTON, 'submit'));
	$jttray->addElement(new XoopsFormButton('', 'addstep', _MD_GWLOTO_JOBSTEP_ADD_BUTTON, 'submit'));
	$jttray->addElement(new XoopsFormButton('', 'print', _MD_GWLOTO_JOB_PRINT_BUTTON, 'submit'));
	$form->addElement($jttray);

	//$form->display();
	$body=$form->render();
}
else { // view only
	// $cplan_name=nl2br($cplan_name);
	$job_description=nl2br($job_description);

	$token=0;

	$formtitle=_MD_GWLOTO_JOB_VIEW_FORM;

	$form = new XoopsThemeForm($formtitle, 'form1', 'viewjob.php', 'POST', $token);

	$caption = _MD_GWLOTO_JOB_NAME;
	$form->addElement(new XoopsFormLabel($caption, htmlspecialchars($job_name, ENT_QUOTES), 'job_name'));

	$caption = _MD_GWLOTO_JOB_STATUS;
	$form->addElement(new XoopsFormLabel($caption, $display_job_status, 'job_status'));

	$caption = _MD_GWLOTO_JOB_WORKORDER;
	$form->addElement(new XoopsFormLabel($caption, htmlspecialchars($job_workorder, ENT_QUOTES), 'job_workorder'));

	$caption = _MD_GWLOTO_JOB_SUPERVISOR;
	$form->addElement(new XoopsFormLabel($caption, htmlspecialchars($job_supervisor, ENT_QUOTES), 'job_supervisor'));

	$caption = _MD_GWLOTO_JOB_STARTDATE;
	$form->addElement(new XoopsFormLabel($caption, htmlspecialchars($job_startdate, ENT_QUOTES), 'job_startdate'));

	$caption = _MD_GWLOTO_JOB_ENDDATE;
	$form->addElement(new XoopsFormLabel($caption, htmlspecialchars($job_enddate, ENT_QUOTES), 'job_enddate'));

	$caption = _MD_GWLOTO_JOB_DESCRIPTION;
	$form->addElement(new XoopsFormLabel($caption, $job_description, 'job_description'));

	$member_handler =& xoops_gethandler('member');
	$thisUser =& $member_handler->getUser($last_changed_by);
        if (!is_object($thisUser) || !$thisUser->isActive() ) {
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

	$caption = _MD_GWLOTO_JOB_TOOL_TRAY_DSC;
	$jttray=new XoopsFormElementTray($caption, '');
	$jttray->addElement(new XoopsFormButton('', 'print', _MD_GWLOTO_JOB_PRINT_BUTTON, 'submit'));
	$form->addElement($jttray);

	//$form->display();
	$body=$form->render();
}

getAttachedMedia('job', $currentjob, $language, $user_can_edit);

$steps=getJobSteps($currentjob);
if(isset($steps)) $xoopsTpl->assign('steps', $steps);

$jobs=getAvailableJobs($myuserid);
if(isset($jobs)) $xoopsTpl->assign('jobs',$jobs);

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//$debug.='<pre>$jobs='.print_r($jobs,true).'</pre>';
//$debug.='<pre>$steps='.print_r($steps,true).'</pre>';

if(isset($body)) $xoopsTpl->assign('body', $body);

setPageTitle(_MD_GWLOTO_TITLE_VIEWJOB);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
