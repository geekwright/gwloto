<?php
/**
* listjobs.php - display a list of jobs matching criteria
*
* This file is part of gwloto - geekwright lockout tagout
*
* @copyright  Copyright © 2010 geekwright, LLC. All rights reserved. 
* @license    gwloto/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwloto
* @version    $Id: listmedia.php 4 2010-09-11 02:19:21Z rgriffith $
*/

include '../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_listjobs.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');
include ('include/jobstatus.php');
$jobstatus['all']=_MD_GWLOTO_JOB_STATUS_ALL; // extra for search

// leave if we don't have any job authority
if(!isset($places['alluserauth'][_GWLOTO_USERAUTH_JB_VIEW]) &&  
   !isset($places['alluserauth'][_GWLOTO_USERAUTH_JB_EDIT])) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

// searching
$jobsearchterms='';
$jobstatusfilter='';
if(isset($_GET['jobsearchterms'])) $jobsearchterms = cleaner($_GET['jobsearchterms']);
if(isset($_POST['jobsearchterms'])) $jobsearchterms = cleaner($_POST['jobsearchterms']);
$xoopsTpl->assign('jobsearchterms',$jobsearchterms);
$jobsearchterms=dbescape($jobsearchterms);
if(isset($_GET['jobstatusfilter'])) $jobstatusfilter = cleaner($_GET['jobstatusfilter']);
if(isset($_POST['jobstatusfilter'])) $jobstatusfilter = cleaner($_POST['jobstatusfilter']);
$xoopsTpl->assign('jobstatusfilter',$jobstatusfilter);
$jobstatusfilter=dbescape($jobstatusfilter);

if($jobsearchterms!='') {
	$searchsql=" AND (job_name like '%$jobsearchterms%' OR job_description like '%$jobsearchterms%' OR job_workorder like '%$jobsearchterms%') ";
}
else $searchsql='';
switch($jobstatusfilter) {
	// delete operations
	case 'all':
		$jobfiltersql=" ";
		break;
	case 'planning':
		$jobfiltersql=" AND job_status = 'planning' ";
		break;
	case 'active':
		$jobfiltersql=" AND job_status = 'active' ";
		break;
	case 'complete':
		$jobfiltersql=" AND job_status = 'complete' ";
		break;
	case 'canceled':
		$jobfiltersql=" AND job_status = 'canceled' ";
		break;
	default:
		$jobfiltersql=" AND (job_status = 'planning' OR job_status='active') ";
		break;
}	
// set up pagenav
	$start=0;
	$limit=20;
	if(isset($_GET['start'])) $start = intval($_GET['start']);

	$sql='SELECT COUNT(*) FROM '. $xoopsDB->prefix('gwloto_job');
	$sql.=" WHERE job_id IN ";
	$sql.='( SELECT job_id FROM '.$xoopsDB->prefix('gwloto_job');
	$sql.=', '.$xoopsDB->prefix('gwloto_job_places');
	$sql.=', '.$xoopsDB->prefix('gwloto_user_auth');
	$sql.=" WHERE uid = $myuserid ";
	$sql.=' AND job = job_id AND place = place_id';
	$sql.=$searchsql;
	$sql.=$jobfiltersql;
	$sql.=' AND (authority='._GWLOTO_USERAUTH_JB_EDIT;
	$sql.=' OR authority='._GWLOTO_USERAUTH_JB_VIEW.') )';
	$total=0;
	$result = $xoopsDB->query($sql);
	if ($result) {
		$myrow=$xoopsDB->fetchRow($result);
		$total=$myrow[0];
	}

	if ($total > $limit) {
	    include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
		$extranav="pid=$currentplace";
		if($jobsearchterms!='') $extranav.="&jobsearchterms=$jobsearchterms";
		if($jobstatusfilter!='') $extranav.="&jobstatusfilter=$jobstatusfilter";
	    $nav = new xoopsPageNav($total,$limit,$start,'start',$extranav);
	    $xoopsTpl->assign('pagenav', $nav->renderNav());
	}

// build an array of jobs
	$jobs=array();

	$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_job');
	$sql.=" WHERE job_id IN ";
	$sql.='( SELECT job_id FROM '.$xoopsDB->prefix('gwloto_job');
	$sql.=', '.$xoopsDB->prefix('gwloto_job_places');
	$sql.=', '.$xoopsDB->prefix('gwloto_user_auth');
	$sql.=" WHERE uid = $myuserid ";
	$sql.=' AND job = job_id AND place = place_id';
	$sql.=$searchsql;
	$sql.=$jobfiltersql;
	$sql.=' AND (authority='._GWLOTO_USERAUTH_JB_EDIT;
	$sql.=' OR authority='._GWLOTO_USERAUTH_JB_VIEW.') )';
	$sql.=' ORDER BY last_changed_on DESC ';

	$result = $xoopsDB->query($sql,$limit,$start);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$i=$myrow['job_id'];
			$jobs[$i]=$myrow;
			$jobs[$i]['display_job_status']=$jobstatus[$myrow['job_status']];
			$jobs[$i]['display_last_changed_on']=getDisplayDate($myrow['last_changed_on']);
			$jobs[$i]['job_name']=htmlspecialchars($myrow['job_name'], ENT_QUOTES);
			$jobs[$i]['job_workorder']=htmlspecialchars($myrow['job_workorder'], ENT_QUOTES);
			$jobs[$i]['job_description']=htmlspecialchars($myrow['job_description'], ENT_QUOTES);

		}
	}

	if(count($jobs)==0) {
		$err_message=_MD_GWLOTO_JOB_NO_JOBS;
	}
	$xoopsTpl->assign('jobs', $jobs);

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//$debug.='<pre>$jobs='.print_r($jobs,true).'</pre>';

if(isset($jobstatus)) $xoopsTpl->assign('jobstatus', $jobstatus);   // for select list

if(isset($actions)) $xoopsTpl->assign('actions', $actions);
if(isset($body)) $xoopsTpl->assign('body', $body);

setPageTitle(_MD_GWLOTO_TITLE_LISTJOBS);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
