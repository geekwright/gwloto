<?php
/**
* jobprintshell.php - setup environment and invoke jobprint plugin
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

//include ('include/common.php');
require_once ('class/gwlotoPrintJob.php');

if(isset($_POST['rptid'])) $currentreport = intval($_POST['rptid']);
else if(isset($_GET['rptid'])) $currentreport = intval($_GET['rptid']);
if(!isset($currentreport)) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
}

// get report parameters
if(isset($currentjob)) unset($currentjob);
if(isset($currentplan)) unset($currentplan);

if(isset($_POST['jid'])) $currentjob = intval($_POST['jid']);
else if(isset($_GET['jid'])) $currentjob = intval($_GET['jid']);

$currentplan=false;
if(isset($_POST['cpid'])) $currentplan = intval($_POST['cpid']);
else if(isset($_GET['cpid'])) $currentplan = intval($_GET['cpid']);

if(!isset($currentjob)) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
}

$myuserid=0;
if($xoopsUser) {
	$myuserid = $xoopsUser->getVar('uid');
}
$language=0; // default language
$userinfo=getUserInfo($myuserid);
if($userinfo) {
	$language=$userinfo['language_id'];
}
if(isset($_POST['lid'])) $language = intval($_POST['lid']);
else if(isset($_GET['lid'])) $language = intval($_GET['lid']);

$user_can_view=false;
if(!$user_can_view) $user_can_view=checkJobAuthority($currentjob,$myuserid,false);
// leave if we don't have any  authority
if(!$user_can_view) {
		$err_message = _MD_GWLOTO_MSG_NO_AUTHORITY;
		redirect_header('index.php', 3, $err_message);
}

// get report definition
$reports=getJobReports($language);

// load print specific language files
if(isset($reports[$currentreport]['language_file']) && $reports[$currentreport]['language_file']!='') {
	$langfolders=getLanguageFolders();
	$langfile=$reports[$currentreport]['language_file'];
	foreach($langfolders as $lid => $folder) {
		$LANGID=$lid;
		if ( file_exists('plugins/language/'.$folder.'/'.$langfile )) {
			include 'plugins/language/'.$folder.'/'.$langfile;
		} else {
			include 'plugins/language/english/'.$langfile;
		}
	}
}
error_reporting(E_ALL & ~E_NOTICE);
$xoopsLogger->activated = false;
require 'plugins/'.$reports[$currentreport]['filename'];

//echo '<pre>$reports='.print_r($reports,true).'</pre>';

//include(XOOPS_ROOT_PATH.'/footer.php');
?>
