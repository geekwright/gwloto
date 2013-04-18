<?php
/**
* common.php - common function definitions
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

if (!defined("XOOPS_ROOT_PATH")) die("Root path not defined");

include_once ('include/dbcommon.php');
include_once ('include/userauth.php');
include_once ('include/jobstatus.php');
include_once ('include/seqoptions.php');
include_once ('include/mediaclass.php');

function setPageTitle($title,$headingonly=false) {
global $xoopsTpl;
	if(!$headingonly) {
		@$xoopsTpl->assign('xoops_pagetitle', _MD_GWLOTO_TITLE_SHORT.$title); // html title
//		@$xoopsTpl->assign('icms_pagetitle', _MD_GWLOTO_TITLE_SHORT.$title);
	}
	$xoopsTpl->assign('title',$title);	// content heading
}

function getUserInfo($uid) {
global $xoopsDB,$xoopsUser;

	$userinfo=false;

	$sql='SELECT * FROM ' . $xoopsDB->prefix('gwloto_user');
	$sql.=" WHERE uid=$uid";

	$result = $xoopsDB->query($sql);
	if ($result) {
		if($myrow=$xoopsDB->fetchArray($result)) {
			$userinfo=$myrow;
		}
	}

//	if($xoopsUser) {
//		if(!is_array($userinfo)) $userinfo=array();
//		$userinfo['userlanguage'] = $xoopsUser->language();
//	}

	return $userinfo;
}

function getUserNameFromId($uid) {
	global $member_handler;

	$thisUser =& $member_handler->getUser($uid);
        if (!is_object($thisUser)) {
			$user_name=$uid;
	} else {
		$user_name = $thisUser->getVar('name');
		if($user_name=='') $user_name = $thisUser->getVar('uname');
	}
	return $user_name;
}

function setClipboard($uid,$clipboard_type='',$clipboard_id=0) {
	global $xoopsDB;

	if($uid!=0) {
		$sql ='UPDATE '.$xoopsDB->prefix('gwloto_user');
		$sql.=" SET clipboard_type = '$clipboard_type' ";
		$sql.=" , clipboard_id = $clipboard_id ";
		$sql.=" WHERE uid = $uid";
		$result = $xoopsDB->queryF($sql);
		$rcnt=$xoopsDB->getAffectedRows();
		if($rcnt==0) {
			$sql ='INSERT into '.$xoopsDB->prefix('gwloto_user');
			$sql.="(uid, clipboard_type, clipboard_id, last_changed_by, last_changed_on) ";
			$sql.="VALUES ($uid, '$clipboard_type', '$clipboard_id', $uid, UNIX_TIMESTAMP())";
			$result = $xoopsDB->queryF($sql);
		}
	}
	return true;
}

function getPlaceName($place_id, $language) {
global $xoopsDB;
static $localplaceid=0;
static $localplacename=false;

	if($localplaceid==$place_id) return($localplacename);

	$sql='SELECT language_id, place_name FROM '.$xoopsDB->prefix('gwloto_place_detail');
	$sql.=" WHERE place = $place_id and (language_id=$language OR language_id=0)";
	$sql.=' ORDER BY language_id ';

	$cnt=0;
	$result = $xoopsDB->query($sql);
	$localplaceid=$place_id;
	$localplacename=false;
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$localplacename=$myrow['place_name'];
		}
	}
	return $localplacename;
}

function getMultiPlaceNames($place_array, $language) {
global $xoopsDB;

	$placenames=array();

	$inclause='';
	foreach($place_array as $v) {
		$i=intval($v);
		if($inclause!='') $inclause.=',';
		$inclause.="$i";
	}

	$sql='SELECT place, language_id, place_name FROM '.$xoopsDB->prefix('gwloto_place_detail');
	$sql.=" WHERE place IN ($inclause) and (language_id=$language OR language_id=0)";
	$sql.=' ORDER BY place, language_id ';

	$cnt=0;
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$placenames[$myrow['place']]=$myrow['place_name'];
		}
	}
	return $placenames;
}

function getCplanName($cplan_id, $language) {
global $xoopsDB;
static $localcplanid=0;
static $localcplanname=false;

	if($localcplanid==$cplan_id) return($localcplanname);

	$sql='SELECT language_id, cplan_name FROM '.$xoopsDB->prefix('gwloto_cplan_detail');
	$sql.=" WHERE cplan = $cplan_id and (language_id=$language OR language_id=0)";
	$sql.=' ORDER BY language_id ';

	$cnt=0;
	$result = $xoopsDB->query($sql);
	$localcplanid=$cplan_id;
	$localcplanname=false;
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$localcplanname=$myrow['cplan_name'];
		}
	}
	return $localcplanname;
}

function getCpointName($cpoint_id, $language) {
global $xoopsDB;
static $localcpointid=0;
static $localcpointname=false;

	if($localcpointid==$cpoint_id) return($localcpointname);

	$sql='SELECT language_id, cpoint_name FROM '.$xoopsDB->prefix('gwloto_cpoint_detail');
	$sql.=" WHERE cpoint = $cpoint_id and (language_id=$language OR language_id=0)";
	$sql.=' ORDER BY language_id ';

	$cnt=0;
	$result = $xoopsDB->query($sql);
	$localcpointid=$cpoint_id;
	$localcpointname=false;
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$localcpointname=$myrow['cpoint_name'];
		}
	}
	return $localcpointname;
}

function getLanguages() {
	global $xoopsDB;
	$langs=array();

	$sql='SELECT language_id, language FROM '.$xoopsDB->prefix('gwloto_language');
	$sql.=' ORDER BY language_id ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$langs[$myrow['language_id']] = $myrow['language'];
		}
	}
	return $langs;
}

function getLanguageCodes() {
	global $xoopsDB;
	$langs=array();

	$sql='SELECT language_id, language_code FROM '.$xoopsDB->prefix('gwloto_language');
	$sql.=' ORDER BY language_id ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$langs[$myrow['language_id']] = $myrow['language_code'];
		}
	}
	return $langs;
}

function getLanguageFolders() {
	global $xoopsDB;
	$langs=array();

	$sql='SELECT language_id, language_folder FROM '.$xoopsDB->prefix('gwloto_language');
	$sql.=' ORDER BY language_id ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$langs[$myrow['language_id']] = $myrow['language_folder'];
		}
	}
	return $langs;
}

function getLanguageByFolder() {
	global $xoopsDB;
	$langs=array();

	$sql='SELECT language_id, language_folder FROM '.$xoopsDB->prefix('gwloto_language');
	$sql.=' ORDER BY language_folder ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$langs[$myrow['language_folder']] = $myrow['language_id'];
		}
	}
	return $langs;
}

function getControlPlans($place_id, $language) {

	global $xoopsDB;
	$cplans=array();

	$sql='SELECT cplan_id, language_id, cplan_name FROM '. $xoopsDB->prefix('gwloto_cplan').', '.$xoopsDB->prefix('gwloto_cplan_detail');
	$sql.= " WHERE place_id = $place_id AND (language_id=$language OR language_id=0) ";
	$sql.= ' AND cplan = cplan_id ';
	$sql.= ' ORDER BY language_id ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$cplans[$myrow['cplan_id']] = $myrow['cplan_name'];
		}
	}
	asort($cplans);
	return $cplans;
}

function getControlPoints($cplan_id, $language, $orderby='seq_disconnect') {
	global $xoopsDB;
	$cpoints=array();

	$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_cpoint').', '.$xoopsDB->prefix('gwloto_cpoint_detail');
	$sql.= " WHERE cplan_id = $cplan_id AND (language_id=$language OR language_id=0) ";
	$sql.= ' AND cpoint = cpoint_id ';
	$sql.= " ORDER BY  $orderby, language_id ";
	
	$cnt=0;
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$i=$myrow['cpoint_id'];
			$cpoints[$i]=$myrow;
		}
	}
	return $cpoints;
}

function getCPointCounts($cplan_id) {
	global $xoopsDB;
	$cpointcnt=array('count'=>0, 'locks'=>0, 'tags'=>0);

	$sql='SELECT count(*) as count, sum(locks_required) as locks, sum(tags_required) as tags FROM '. $xoopsDB->prefix('gwloto_cpoint');
	$sql.= " WHERE cplan_id = $cplan_id ";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		$myrow=$xoopsDB->fetchArray($result);
		if($myrow && $myrow['count']>0) {
			$cpointcnt['count']=$myrow['count'];
			$cpointcnt['locks']=$myrow['locks'];
			$cpointcnt['tags']=$myrow['tags'];
		}
	}

	return $cpointcnt;
}

function getCPointTranslateStats($cplan_id) {
	global $xoopsDB;
	$cpointcnt=array();

	$langs=getLanguages();
	foreach($langs as $i => $v) {
		$cpointcnt[$i]['count']=0;
		$cpointcnt[$i]['changedate']='';
		$cpointcnt[$i]['language']=$v;
	}

	$sql='SELECT language_id, count(*) as count, max(last_changed_on) as changedate FROM '. $xoopsDB->prefix('gwloto_cpoint').', '. $xoopsDB->prefix('gwloto_cpoint_detail');
	$sql.= " WHERE cplan_id = $cplan_id AND cpoint_id = cpoint";
	$sql.= " GROUP BY language_id ";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			if($myrow && $myrow['count']>0) {
				$lid=$myrow['language_id'];
				$cpointcnt[$lid]['count']=$myrow['count'];
				$cpointcnt[$lid]['changedate']=getDisplayDate($myrow['changedate']);
				$cpointcnt[$lid]['language']=$langs[$lid];
			}
		}
	}

	return $cpointcnt;
}


function getDisplayDate($timestamp) {
global $xoopsModuleConfig;
	$pref_date=$xoopsModuleConfig['pref_date'];
	return formatTimeStamp($timestamp,$pref_date);
}

function buildPlaceSummary() {
global $places, $xoopsTpl;
$placesummary=array();

	if (isset($places['current'])) {
		$hazard_inventory=nl2br($places['current']['place_hazard_inventory']);
		if($hazard_inventory=='') $hazard_inventory=_MD_GWLOTO_NO_PLACE_HAZARDS;
		$required_ppe=nl2br($places['current']['place_required_ppe']);
		if($required_ppe=='') $required_ppe=_MD_GWLOTO_NO_PLACE_PPE;

		$placesummary[0]['header']=sprintf(_MD_GWLOTO_PLACE_HAZARDS,$places['current']['place_name']);
		$placesummary[0]['detail']=$hazard_inventory;
		$placesummary[1]['header']=sprintf(_MD_GWLOTO_PLACE_PPE,$places['current']['place_name']);
		$placesummary[1]['detail']=$required_ppe;
		
		$xoopsTpl->assign('placesummary', $placesummary);
	}
}

function buildPlaceChain($uid,$pid,&$autharray,&$chainup,&$chaindown,&$allautharray) {
global $xoopsDB;

	$startplace=$pid;
	$inclause=array();;
	$killcnt=100; // just a safety net

	while($startplace!=0) {

		if(isset($sql)) unset($sql);
		if(isset($result)) unset($result);
		if(isset($myrow)) unset($myrow);

		$sql='SELECT place_id, parent_id FROM '.$xoopsDB->prefix('gwloto_place');
		$sql.=" WHERE place_id=$startplace";

		$result = $xoopsDB->query($sql);
		if ($result) {
			while($myrow=$xoopsDB->fetchArray($result)) {
				if(is_array($chainup)) $chainup[$myrow['place_id']]=$myrow['parent_id'];
				if(is_array($chaindown)) $chaindown[$myrow['parent_id']]=$myrow['place_id'];

				$inclause[$myrow['place_id']]=true;

				$startplace=$myrow['parent_id'];
			}
		}
		--$killcnt;
		if($killcnt<0) break;
	}

	if(is_array($autharray)) {
		$sql='SELECT place_id, authority FROM  ' . $xoopsDB->prefix('gwloto_user_auth');
		$sql.=" WHERE uid=$uid ";

	$sql.=' UNION SELECT place_id, authority ';
	$sql.=' FROM '. $xoopsDB->prefix('gwloto_group_auth').' g ';
	$sql.=', '. $xoopsDB->prefix('groups_users_link').' l ';
	$sql.=" WHERE uid=$uid  and g.groupid = l.groupid ";

		$sql.=" ORDER BY authority, place_id ";

		$result = $xoopsDB->query($sql);
		if ($result) {
			while($myrow=$xoopsDB->fetchArray($result)) {
				if(isset($inclause[$myrow['place_id']])) $autharray[$myrow['authority']]=true;
				if(is_array($allautharray)) $allautharray[$myrow['authority']][$myrow['place_id']]=true;
			}
		}
	}
}

function getCplanFromPoint($ptid) {
	global $xoopsDB;
	$cplan=false;
	$sql='SELECT cplan_id FROM '.$xoopsDB->prefix('gwloto_cpoint');
	$sql.=" WHERE cpoint_id = $ptid";
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$cplan=$myrow['cplan_id'];
		}
	}
	return $cplan;
}

function getPlaceFromCplan($cpid) {
	global $xoopsDB;
	$pid=false;
	$sql='SELECT place_id FROM '.$xoopsDB->prefix('gwloto_cplan');
	$sql.=" WHERE cplan_id = $cpid";
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$pid=$myrow['place_id'];
		}
	}
	return $pid;
}

function getPlacesByUserAuth($uid,$authority,$language) {
	global $xoopsDB;
	$pids=array();

	$sql='SELECT distinct(place_id) as place_id FROM '.$xoopsDB->prefix('gwloto_user_auth');
	$sql.=" WHERE uid=$uid AND authority=$authority ";

	$sql.=' UNION distinct(place_id) as place_id ';
	$sql.=' FROM '. $xoopsDB->prefix('gwloto_group_auth').' g ';
	$sql.=', '. $xoopsDB->prefix('groups_users_link').' l ';
	$sql.=" WHERE uid=$uid AND authority=$authority AND g.groupid = l.groupid ";

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$temp_pid=$myrow['place_id'];
			$pids[$temp_pid]=getPlaceName($temp_pid, $language);
		}
	}
	return $pids;
}

function getUsersByAuth($authority,$place_array,$job_id) {
	global $xoopsDB;
	$uids=array();

	if($job_id) {
		$inclause="select place from ".$xoopsDB->prefix('gwloto_job_places')." where job=$job_id";
	} else {
		$inclause='';
		foreach($place_array as $v) {
			$i=intval($v);
			if($inclause!='') $inclause.=',';
			$inclause.="$i";
		}
	}

	$member_handler =& xoops_gethandler('member');

	$sql='SELECT distinct uid as uid FROM '. $xoopsDB->prefix('gwloto_user_auth');
	$sql.=" WHERE authority=$authority AND place_id in ($inclause)";
	$sql.=' UNION SELECT distinct uid as uid ';
	$sql.=' FROM '. $xoopsDB->prefix('gwloto_group_auth').' g ';
	$sql.=', '. $xoopsDB->prefix('groups_users_link').' l ';
	$sql.=" WHERE authority=$authority AND place_id in ($inclause) AND g.groupid = l.groupid ";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$uid=$myrow['uid'];
			$uids[$uid] = getUserNameFromId($uid);
		}
	}
	return $uids;
}

function getAvailableJobs($uid,$limit=20,$start=0) {
	global $xoopsDB,$jobstatus;
	$jobs=array();

	$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_job');
	$sql.=" WHERE job_id IN ";
	$sql.='( SELECT job_id FROM '.$xoopsDB->prefix('gwloto_job');
	$sql.=', '.$xoopsDB->prefix('gwloto_job_places');
	$sql.=', '.$xoopsDB->prefix('gwloto_user_auth');
	$sql.=" WHERE uid = $uid AND (job_status = 'planning' OR job_status='active')";
	$sql.=' AND job = job_id AND place = place_id';
	$sql.=' AND (authority='._GWLOTO_USERAUTH_JB_EDIT;
	$sql.=' OR authority='._GWLOTO_USERAUTH_JB_VIEW.') ';

	$sql.=' UNION SELECT job_id FROM '.$xoopsDB->prefix('gwloto_job');
	$sql.=', '.$xoopsDB->prefix('gwloto_job_places');
	$sql.=', '.$xoopsDB->prefix('gwloto_group_auth').' g ';
	$sql.=', '.$xoopsDB->prefix('groups_users_link').' l ';
	$sql.=" WHERE uid = $uid AND (job_status = 'planning' OR job_status='active')";
	$sql.=' AND g.groupid = l.groupid ';
	$sql.=' AND job = job_id AND place = place_id';
	$sql.=' AND (authority='._GWLOTO_USERAUTH_JB_EDIT;
	$sql.=' OR authority='._GWLOTO_USERAUTH_JB_VIEW.') )';

	$result = $xoopsDB->query($sql,$limit,$start);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$i=$myrow['job_id'];
			$jobs[$i]=$myrow;
			$jobs[$i]['display_job_status']=$jobstatus[$myrow['job_status']];
		}
	}
	return $jobs;
}

function getJobSteps($jid) {
	global $xoopsDB,$stepstatus,$language;
	$steps=array();
	$member_handler =& xoops_gethandler('member');

	$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_job_steps');
	$sql.=" WHERE job = $jid ";

	$result = $xoopsDB->query($sql);
	$i=0;
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$steps[$i]=$myrow;
			$steps[$i]['display_job_step_status']=$stepstatus[$myrow['job_step_status']];
			$steps[$i]['cplan_name']=getCplanName($myrow['cplan'],$language);

			$steps[$i]['assigned_name']=getUserNameFromId($myrow['assigned_uid']);

			++$i;
		}
	}
	return $steps;
}

function getJobCplanIds($jid) {
	global $xoopsDB;
	$steps=array();

	$sql='SELECT cplan FROM '. $xoopsDB->prefix('gwloto_job_steps');
	$sql.=" WHERE job = $jid ";

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$i=$myrow['cplan'];
			$steps[$i]=$i;
		}
	}
	return $steps;
}

function checkJobAuthority($jid,$uid,$neededit=false) {
	global $xoopsDB;

	$jobauth=false;

	$sql='SELECT count(*) as authcount FROM '.$xoopsDB->prefix('gwloto_job_places');
	$sql.=', '.$xoopsDB->prefix('gwloto_group_auth').' g ';
	$sql.=', '.$xoopsDB->prefix('groups_users_link').' l ';
	$sql.=" WHERE uid = $uid AND job = $jid AND place = place_id";
	$sql.=' AND g.groupid = l.groupid ';
	$sql.=' AND (authority='._GWLOTO_USERAUTH_JB_EDIT;
	if(!$neededit) $sql.=' OR authority='._GWLOTO_USERAUTH_JB_VIEW;
	$sql.=') ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		if($myrow=$xoopsDB->fetchArray($result)) {
			$jobauth=intval($myrow['authcount']);
		}
	}
	if($jobauth) return $jobauth;

	$sql='SELECT count(*) as authcount FROM '.$xoopsDB->prefix('gwloto_job_places');
	$sql.=', '.$xoopsDB->prefix('gwloto_user_auth');
	$sql.=" WHERE uid = $uid AND job = $jid AND place = place_id";
	$sql.=' AND (authority='._GWLOTO_USERAUTH_JB_EDIT;
	if(!$neededit) $sql.=' OR authority='._GWLOTO_USERAUTH_JB_VIEW;
	$sql.=') ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		if($myrow=$xoopsDB->fetchArray($result)) {
			$jobauth=intval($myrow['authcount']);
		}
	}
	return $jobauth;
}

function getJobName($jid,$language) {
	global $xoopsDB;
	$jobname=false;

	$sql='SELECT job_name FROM '. $xoopsDB->prefix('gwloto_job');
	$sql.=" WHERE job_id = $jid ";
	$result = $xoopsDB->query($sql);
	if ($result) {
		if($myrow=$xoopsDB->fetchArray($result)) {
			$jobname=$myrow['job_name'];
		}
	}
	return $jobname;
}

function getJobReqs() {
	global $xoopsModuleConfig;

	$jobreqs['workorder']=false;
	$jobreqs['supervisor']=false;
	$jobreqs['startdate']=false;
	$jobreqs['enddate']=false;
	$jobreqs['description']=false;
	$jobreqs['stepname']=false;
	$jobrequires=explode(',',$xoopsModuleConfig['jobrequires']);

	foreach($jobrequires as $v) {
		if(isset($jobreqs[$v])) $jobreqs[$v]=true;
	}

	return $jobreqs;
}

function getPlanReqs() {
	global $xoopsModuleConfig;
	$planreqs['review']=false;
	$planreqs['hazard_inventory']=false;
	$planreqs['required_ppe']=false;
	$planreqs['authorized_personnel']=false;
	$planreqs['additional_requirements']=false;
	$planreqs['disconnect_instructions']=false;
	$planreqs['reconnect_instructions']=false;
	$planreqs['inspection_instructions']=false;
	$planreqs['inspection_state']=false;


	$planrequires=explode(',',$xoopsModuleConfig['planrequires']);

	foreach($planrequires as $v) {
		if(isset($planreqs[$v])) $planreqs[$v]=true;
	}

	return $planreqs;
}

function getJobReports($language=0) {

	global $xoopsDB;
	$reports=array();

	$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_plugin_register').', '.$xoopsDB->prefix('gwloto_plugin_name');
	$sql.= " WHERE plugin_type = 'jobprint' AND (language_id=$language OR language_id=0) ";
	$sql.= ' AND plugin = plugin_id ';
	$sql.= " ORDER BY  plugin_seq, language_id ";
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$i=$myrow['plugin_id'];
			$reports[$i]=array(
				'plugin_id'=>$myrow['plugin_id'],
				'plugin_seq'=>$myrow['plugin_seq'],
				'type'=>$myrow['plugin_type'],
				'link'=>$myrow['plugin_link'],
				'filename'=>$myrow['plugin_filename'],
				'language_file'=>$myrow['plugin_language_filename'],
				'name'=>$myrow['plugin_name'],
				'description'=>$myrow['plugin_description']);
		}
	}

	return $reports;
}

function getMaxMediaSize() {
	global $xoopsModuleConfig;

	$max_media_size=intval($xoopsModuleConfig['max_media_size']);
	if($max_media_size<150000) $max_media_size=150000;
	return $max_media_size;
}

function getMediaName($media_id, $language) {
global $xoopsDB;

	$sql='SELECT language_id, media_name FROM '.$xoopsDB->prefix('gwloto_media_detail');
	$sql.=" WHERE media = $media_id and (language_id=$language OR language_id=0)";
	$sql.=' ORDER BY language_id ';

	$result = $xoopsDB->query($sql);
	$medianame=false;
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$medianame=$myrow['media_name'];
		}
	}
	return $medianame;
}

function getAttachedMedia($attach_type, $generic_id, $language, $canedit) {
global $xoopsDB, $xoopsTpl;
global $mediaclass;
$media=array();

	$sql ="SELECT media_attach_id, ma.media_id as media_id, media_order, required";
	$sql.=", language_id, media_name, media_description, media_class ";
	$sql.='FROM '.$xoopsDB->prefix('gwloto_media_attach').' ma, ';
	$sql.=$xoopsDB->prefix('gwloto_media').' m, ';
	$sql.=$xoopsDB->prefix('gwloto_media_detail');

	$sql.=" WHERE attach_type = '$attach_type' AND generic_id = $generic_id ";
	$sql.=' AND media = ma.media_id AND m.media_id=ma.media_id ';
	$sql.=" AND (language_id=$language OR language_id=0) ";

	$sql.="ORDER BY required desc, media_order, ma.media_id, language_id ";

	$result = $xoopsDB->query($sql);

	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$media[$myrow['media_id']]=$myrow;
			if($myrow['required']) $media[$myrow['media_id']]['display_required']='*';
			else $media[$myrow['media_id']]['display_required']='';
			$media[$myrow['media_id']]['display_media_class']=$mediaclass[$myrow['media_class']];
		}
	}
	$xoopsTpl->assign('media',$media);
	if($canedit) {
		$xoopsTpl->assign('attach_type',$attach_type);
		$xoopsTpl->assign('generic_id',$generic_id);
	}
	return $media;

}
// end of common stuff
?>
