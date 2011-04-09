<?php
/**
* index.php - place browser
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

include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/userauth.php');
include ('include/userauthlist.php');
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');
include ('include/jobstatus.php');

if(!isset($currentplace) && !isset($places['choose'])) {
	if($myuserid==0) {
		$err_message = _MD_GWLOTO_MSG_ANON_ACCESS;
	} else {
		$err_message = _MD_GWLOTO_MSG_NO_ACCESS;
	}
}

if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_CP_VIEW]) ||
isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS]) ) {
	$cplans = getControlPlans($currentplace, $language);
}

buildPlaceSummary();
if (isset($currentplace)) getAttachedMedia('place', $currentplace, $language, false);

$jobs=getAvailableJobs($myuserid);

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
$debug='<pre>$places='.print_r($places,true).'</pre>';
//if(isset($cplans)) $debug.='<pre>cplans='.print_r($cplans,true).'</pre>';
//$debug.='<pre>$_SESSION='.print_r($_SESSION,true).'</pre>';
//$debug.='<pre>$xoopsConfig='.print_r($xoopsConfig,true).'</pre>';

setPageTitle(_MD_GWLOTO_TITLE_INDEX);

if(isset($jobs)) $xoopsTpl->assign('jobs',$jobs);

if(isset($actions)) $xoopsTpl->assign('actions', $actions);
if(isset($cplans)) $xoopsTpl->assign('cplans', $cplans);
if(isset($body)) $xoopsTpl->assign('body', $body);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
