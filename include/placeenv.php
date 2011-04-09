<?php
if (!defined("XOOPS_ROOT_PATH")) die("Root path not defined");
/**
* some verbage
* define some common functions and establish the base place environment
*
* defines:
*  $myuserid     - uid of current user
*  $currentplace - place_id (pid) of active place
*  $currentplan  - cplan_id (cpid) of active plan
*  $currentpoint - cpoint_id (ptid) of active point
*  $currentjob   - job_id (jid) of active job
*  $currentmedia - media_id (mid)
*  $defaultplace - default place_id of current user
*  $language     - language_id for current user
*  $places       - base place environment, an array of names, authorities, etc.
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

$mymoduledir='modules/'.$xoopsModule->getInfo('dirname').'/';
$xoTheme->addStylesheet($mymoduledir.'module.css');

$myuserid=0;
if($xoopsUser) {
	$myuserid = $xoopsUser->getVar('uid');
}

if(isset($currentplace)) unset($currentplace);
if(isset($currentplan)) unset($currentplan);
if(isset($currentpoint)) unset($currentpoint);
if(isset($currentjob)) unset($currentjob);
if(isset($currentjobstep)) unset($currentjobstep);
if(isset($currentmedia)) unset($currentmedia);

// media specified
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
	else {
		unset($currentmedia);
	}
}

// control point specified
if(isset($_POST['ptid'])) $currentpoint = intval($_POST['ptid']);
else if(isset($_GET['ptid'])) $currentpoint = intval($_GET['ptid']);

if(isset($currentpoint)) {
	$currentplan=getCplanFromPoint($currentpoint);
	if(!$currentplan) {
		unset($currentplan);
		unset($currentpoint);
	}
}

// job_step_id specified - expand to job and plan
if(isset($_POST['jsid'])) $currentjobstep = intval($_POST['jsid']);
else if(isset($_GET['jsid'])) $currentjobstep = intval($_GET['jsid']);

if(isset($currentjobstep)) {
	$sql='SELECT job, cplan FROM '.$xoopsDB->prefix('gwloto_job_steps');
	$sql.=" WHERE job_step_id = $currentjobstep";
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$currentjob=$myrow['job'];
			$currentplan=$myrow['cplan'];
		}
	}
	else {
		unset($currentjobstep);
	}
}

if(isset($_POST['jid'])) $currentjob = intval($_POST['jid']);
else if(isset($_GET['jid'])) $currentjob = intval($_GET['jid']);

if(isset($_POST['cpid'])) $currentplan = intval($_POST['cpid']);
else if(isset($_GET['cpid'])) $currentplan = intval($_GET['cpid']);

if(isset($currentjob) && !isset($currentplan)) {
	// grab first plan associated with job to use to set place
	$sql='SELECT cplan FROM '.$xoopsDB->prefix('gwloto_job_steps');
	$sql.=" WHERE job = $currentjob";
	$result = $xoopsDB->query($sql);
	if ($result) {
		if($myrow=$xoopsDB->fetchArray($result)) {
			$currentplan=$myrow['cplan'];
		}
	}
}

if(isset($currentplan)) {
	$currentplace=getPlaceFromCplan($currentplan);
	if(!$currentplace) {
		unset($currentplace);
		unset($currentplan);
	}
}

if(isset($_POST['pid'])) $currentplace = intval($_POST['pid']);
else if(isset($_GET['pid'])) $currentplace = intval($_GET['pid']);

$places=array();

if(isset($defaultplace)) unset($defaultplace);
$language=0; // default language

$userinfo=getUserInfo($myuserid);

if($userinfo) {
	if($userinfo['default_place_id']!=0) {
		$places['default']=$userinfo['default_place_id'];
		$defaultplace=$userinfo['default_place_id'];
	}
	$language=$userinfo['language_id'];
	$places['userinfo']=$userinfo;
}

if(isset($system_language)) unset($system_langage);

if(isset($xoopsConfig['language']))  $system_language=$xoopsConfig['language'];
if(isset($icmsConfig['language']))   $system_language=$icmsConfig['language'];
if(isset($_SESSION['UserLanguage'])) $system_language=$_SESSION['UserLanguage'];

if(isset($system_language)) {
	$langfolders=getLanguageByFolder();
	if(isset($langfolders[$system_language])) $language=$langfolders[$system_language];
}

if(isset($_POST['lid'])) $language = intval($_POST['lid']);
else if(isset($_GET['lid'])) $language = intval($_GET['lid']);

if((!isset($currentplace) && !isset($defaultplace)) || (isset($currentplace) && $currentplace==0)) {
	$sql='SELECT distinct(place_id) as place_id FROM '.$xoopsDB->prefix('gwloto_user_auth');
	$sql.=" WHERE uid=$myuserid";
	$sql.=' UNION SELECT distinct(place_id) as place_id ';
	$sql.=' FROM '. $xoopsDB->prefix('gwloto_group_auth').' g ';
	$sql.=', '. $xoopsDB->prefix('groups_users_link').' l ';
	$sql.=" WHERE uid=$myuserid and g.groupid = l.groupid ";

	$result = $xoopsDB->query($sql);
	$cnt=0;
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$temp_pid=$myrow['place_id'];
			$places['auth'][$temp_pid]=getPlaceName($temp_pid, $language);
			++$cnt;
		}
		if(!isset($defaultplace) && $cnt==1) {
			$defaultplace=$temp_pid;
			$places['default']=$defaultplace;
//			if(isset($currentplace) && $currentplace==0) $currentplace=$defaultplace;
		}
	}
}

if(isset($defaultplace) && !isset($currentplace)) {
	$currentplace=$defaultplace;
}

if(isset($currentplace) && $currentplace==0) unset($currentplace);

if(isset($currentplace)) {
	$startplace=$currentplace;
	$places['currentauth']=array();
	$places['chainup']=array();
	$places['chaindown']=array();
	$places['alluserauth']=array();
	buildPlaceChain($myuserid,$currentplace,$places['currentauth'],$places['chainup'],$places['chaindown'],$places['alluserauth']);

	$places['name']=getMultiPlaceNames($places['chaindown'], $language);

	if(count($places['currentauth'])==0) { // no authority found
		// if this is a job, we will check deeper later, so don't leave
		if(!isset($currentjob)) redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
	}

	$cnt=0;
	$startplace=0;
	while(isset($places['chaindown'][$startplace])) {
		$startplace=$places['chaindown'][$startplace];
		$places['crumbs'][$cnt]['id']=$startplace;
		$places['crumbs'][$cnt]['name']=$places['name'][$startplace];
		++$cnt;
	}

}


// set places['choose'] to a valid list of choices
if(isset($currentplace)) {
	$startplace=$currentplace;

	if(isset($sql)) unset($sql);
	if(isset($result)) unset($result);
	if(isset($myrow)) unset($myrow);

	$sql='SELECT place_id FROM '.$xoopsDB->prefix('gwloto_place');
	$sql.=" WHERE parent_id=$startplace";

	$tmppid=array();
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$tmppid[$myrow['place_id']]=$myrow['place_id'];
//			$places['choose'][$temp_pid]=getPlaceName($temp_pid, $language);
		}
		if(count($tmppid)>0) $places['choose']=getMultiPlaceNames($tmppid, $language);
	}

}
else {	// no current place, use auth results as choose
	if(isset($places['auth'])) {
		$places['choose']=$places['auth'];
	}
}

if(isset($places['choose'])) asort($places['choose']);

if(isset($currentplace)) {
	$sql='SELECT * FROM '.$xoopsDB->prefix('gwloto_place_detail');
	$sql.=" WHERE place = $currentplace and (language_id=$language OR language_id=0)";
	$sql.=' ORDER BY language_id ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$places['current']=$myrow;
		}
	}
}

// make $current* available to templates
if(isset($currentplace)) $xoopsTpl->assign('currentplace', $currentplace);
if(isset($currentplan))  $xoopsTpl->assign('currentplan',  $currentplan);
if(isset($currentpoint)) $xoopsTpl->assign('currentpoint', $currentpoint);
if(isset($currentjob))   $xoopsTpl->assign('currentjob',   $currentjob);
if(isset($currentmedia)) $xoopsTpl->assign('currentmedia', $currentmedia);
if(isset($language)) $xoopsTpl->assign('language', $language);
// end of common stuff

?>