<?php
/**
* printjob.php - display list of jobprint plugins and dispatch print
* scripts based on selection of a complete job, or a single jobstep
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
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_printjob.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/userauth.php');
include ('include/userauthlist.php');
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');
include ('include/jobstatus.php');

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
// leave if we don't have any authority
if(!$user_can_view) {
		$err_message = _MD_GWLOTO_MSG_NO_AUTHORITY;
		redirect_header('index.php', 3, $err_message);
}

$currentplan=false;
if(isset($_POST['cpid'])) $currentplan = intval($_POST['cpid']);
else if(isset($_GET['cpid'])) $currentplan = intval($_GET['cpid']);

$reports=getJobReports($language);

if(count($reports)==0) $err_message = _MD_GWLOTO_JOB_PRINT_NODEFS;
$currentreport=false;
foreach($reports as $i=>$v) { if($currentreport==false) $currentreport=$i; }
if(isset($_POST['rptid'])) $currentreport = intval($_POST['rptid']);
else if(isset($_GET['rptid'])) $currentreport = intval($_GET['rptid']);

$op='display';
if(isset($_POST['view'])) {
	redirect_header("viewjob.php?jid=$currentjob", 3, _MD_GWLOTO_JOB_VIEW_REDIR_MSG);
}
if(isset($_POST['print']) && $currentreport) {
	$prtmsg = _MD_GWLOTO_JOB_PRINTING_REDIR_MSG;
	$args="rptid=$currentreport";
	$args.="&jid=$currentjob";
	$args.="&lid=$language";
	$args.="&seq=$currentseq";
	if($currentplan) $args.="&cpid=$currentplan";
	parse_str($args, $_GET);
	parse_str($args, $_POST);
	require $reports[$currentreport]['link'];
	exit;
//	redirect_header($reports[$currentreport]['link'].'?'.$args, 3, $prtmsg);
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
		}
	}
	else {
		redirect_header('index.php', 3, _MD_GWLOTO_JOB_NOTFOUND);
	}

	$myts = myTextSanitizer::getInstance();
	// $cplan_name=$myts->nl2Br($cplan_name);
	$job_description=$myts->nl2Br($job_description);

$step_name='';
$assigned_uid=$myuserid;
$job_step_status='planning';
// get data from steps table
if($currentplan) {
	$sql='SELECT * FROM '.$xoopsDB->prefix('gwloto_job_steps');
	$sql.=" WHERE job = $currentjob";
	$sql.=" AND cplan = $currentplan ";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$step_name=$myrow['step_name'];
			$assigned_uid=$myrow['assigned_uid'];
			$job_step_status = $myrow['job_step_status'];
			$display_job_step_status=$stepstatus[$job_step_status];
		}
	}
}

	$token=0;

	$formtitle=_MD_GWLOTO_JOB_PRINT_FORM;

	$form = new XoopsThemeForm($formtitle, 'form1', 'printjob.php', 'POST', $token);
	$form->setExtra(' target="_blank" ');

	$caption = _MD_GWLOTO_JOB_NAME;
	$form->addElement(new XoopsFormLabel($caption, htmlspecialchars($job_name, ENT_QUOTES), 'job_name'));

if($currentplan)  {
	$caption = _MD_GWLOTO_JOBSTEP_PLAN;
	$cplan_name=htmlspecialchars(getCplanName($currentplan, $language), ENT_QUOTES);
	$form->addElement(new XoopsFormLabel($caption, '<a href="viewplan.php?cpid='.$currentplan.'">'.$cplan_name.'</a>', 'cplan_name'),false);

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

}

	$caption = _MD_GWLOTO_JOB_STATUS;
	$form->addElement(new XoopsFormLabel($caption, $display_job_status, 'job_status'));

if($currentplan)  {
	$caption = _MD_GWLOTO_JOBSTEP_STATUS;
	$form->addElement(new XoopsFormLabel($caption, $display_job_step_status, 'job_step_status'));

}

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

	$form->addElement(new XoopsFormHidden('jid', $currentjob));

	if($currentplan) $form->addElement(new XoopsFormHidden('cpid', $currentplan));

	$caption = _MD_GWLOTO_JOB_PRINT_PICK;
	$rptradio=new XoopsFormRadio($caption, 'rptid', $currentreport, '<br />');

	foreach($reports as $i=>$v) { 
		$rptradio->addOption($i, $v['name']);
	}
	$form->addElement($rptradio,true);

	if(count($seqoptions)>1) {
		$caption = _MD_GWLOTO_JOB_PHASE_SEQ;
		$radio=new XoopsFormRadio($caption, 'seq', $currentseq, '');

		foreach($seqoptions as $i => $v) {
			$radio->addOption($i, $v['label']);
		}
		$form->addElement($radio);
	}


	$caption = _MD_GWLOTO_JOB_TOOL_TRAY_DSC;
	$jttray=new XoopsFormElementTray($caption, '');
	$jttray->addElement(new XoopsFormButton('', 'print', _MD_GWLOTO_JOB_PRINT_BUTTON, 'submit'));
	if($user_can_edit) $buttontext=_MD_GWLOTO_JOB_PREDIT_BUTTON;
	else $buttontext=_MD_GWLOTO_JOB_PRVIEW_BUTTON;
	$viewbutton = new XoopsFormButton('', 'view', $buttontext, 'submit');
	$viewbutton->setExtra(' onClick=\'this.form.target = "_self"\' ');
	$jttray->addElement($viewbutton);
	$form->addElement($jttray);
	//$form->display();
	$body=$form->render();


$steps=getJobSteps($currentjob);
if(isset($steps)) $xoopsTpl->assign('steps', $steps);

$jobs=getAvailableJobs($myuserid);
if(isset($jobs)) $xoopsTpl->assign('jobs',$jobs);

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//$debug.='<pre>$jobs='.print_r($jobs,true).'</pre>';
//$debug.='<pre>$steps='.print_r($steps,true).'</pre>';

if(isset($body)) $xoopsTpl->assign('body', $body);

setPageTitle(_MD_GWLOTO_TITLE_PRINTJOB);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
