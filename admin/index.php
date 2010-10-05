<?php
/**
* index.php - admin page for about and configuration messages
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

include ('../../../include/cp_header.php');
xoops_cp_header();
include_once "functions.php";
include_once "../include/dbcommon.php";
adminmenu(1);

// build todo list
$todo = array();
$todocnt = 0;

$op='';
if(isset($fixmp_status)) unset($fixmp_status);
if(isset($_GET['op'])) $op = cleaner($_GET['op']);
if($op=='fixmp') {
	// try and make the upload directory
	$pathname=getMediaUploadPath();
	$mode=0700;
	$recursive=true;
	$fixmp_status=mkdir($pathname,$mode,$recursive);
}

// check mysql version
$mysqlversion_required='4.0.0';

$sql="select version() as version";
$result = $xoopsDB->queryF($sql);
if ($result) {
	while($myrow=$xoopsDB->fetchArray($result)) {
		$mysqlversion=$myrow['version'];
	}
	if(version_compare($mysqlversion,$mysqlversion_required) < 0) {
		++$todocnt;
		$todo[$todocnt]['link']='index.php';
		$todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_RETRY;
		$todo[$todocnt]['msg']= sprintf(_MI_GWLOTO_AD_TODO_MYSQL, $mysqlversion_required, $mysqlversion);
	}
}

if (false && function_exists('curl_init')) {
	// get cuurent version with curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://bigtux/version.txt");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $current_version = curl_exec($ch);
        curl_close($ch);

	$installed_version = $xoopsModule->getInfo('version');

	if(version_compare($installed_version,$current_version) < 0) {
		++$todocnt;
		$todo[$todocnt]['link']='index.php';
		$todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_RETRY;
		$todo[$todocnt]['msg']= sprintf(_MI_GWLOTO_AD_TODO_UPGRADE,$installed_version,$current_version);
	}
}

// check for tcpdf
	$tcpdf_path='';
	$tcpdf_path=$xoopsModuleConfig['tcpdf_path'];
	if ($tcpdf_path!='' && !file_exists($tcpdf_path) ) {
		++$todocnt;
		$todo[$todocnt]['link']=XOOPS_URL . "/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid');
		$todo[$todocnt]['linktext']=_MI_GWLOTO_ADMENU_PREF;
		$todo[$todocnt]['msg']= _MI_GWLOTO_AD_TODO_TCPDF_NOTFND;
	}
	else {
		if($tcpdf_path=='') {
			if(!file_exists('../tcpdf/tcpdf.php') && !file_exists(XOOPS_ROOT_PATH.'/libraries/tcpdf/tcpdf.php')) {
				++$todocnt;
				$todo[$todocnt]['link']='index.php';
				$todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_RETRY;
				$todo[$todocnt]['msg']= _MI_GWLOTO_AD_TODO_TCPDF_INSTALL .sprintf(_MI_GWLOTO_AD_TODO_TCPDF_GENERAL,XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/tcpdf/');
			} else {
				if (!file_exists('../tcpdf/tcpdf.php') ) {
					++$todocnt;
					$todo[$todocnt]['link']='index.php';
					$todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_RETRY;
					$todo[$todocnt]['msg']= _MI_GWLOTO_AD_TODO_TCPDF_UPGRADE .sprintf(_MI_GWLOTO_AD_TODO_TCPDF_GENERAL,XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/tcpdf/');
				}
			}
		}
	}

// check for a top level place
$sql="SELECT count(*) as rowcount FROM ".$xoopsDB->prefix('gwloto_place')." WHERE parent_id=0";
$result = $xoopsDB->query($sql);
$cnt=0;
if ($result) {
	$myrow=$xoopsDB->fetchArray($result);
	$cnt=$myrow['rowcount'];
}
if($cnt==0) {
	++$todocnt;
	$todo[$todocnt]['link']='addplace.php';
	$todo[$todocnt]['linktext']=_MI_GWLOTO_AD_PLACE_EDIT_BUTTON;
	$todo[$todocnt]['msg']= _MI_GWLOTO_AD_TODO_PLACES;
}

// check media upload directory permissions
$pathname=getMediaUploadPath();
if(!is_writable($pathname)) {
	++$todocnt;
	$todo[$todocnt]['link']='index.php?op=fixmp';
	if(isset($fixmp_status)) $todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_FIX_FAILED;
	else $todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_FIX;
	$todo[$todocnt]['msg']= sprintf(_MI_GWLOTO_AD_TODO_UPLOAD,$pathname);
}

// display todo list
if($todocnt>0) {
	$teven='class="even"';
	$todd='class="odd"';
	$tclass=$teven;
	echo '<table width="100%" border="1" cellspacing="1" class="outer">';
	echo  '<tr><th colspan="2">'._MI_GWLOTO_AD_TODO_TITLE.'</th></tr>';
	echo '<tr><th width="25%">'._MI_GWLOTO_AD_TODO_ACTION.'</th><th>'._MI_GWLOTO_AD_TODO_MESSAGE.'</th></tr>';

	for ($i=1; $i<=$todocnt; ++$i) {
		if($tclass==$todd) $tclass=$teven;
		else $tclass=$todd;
		echo '<tr cellspacing="2" cellpadding="2" '.$tclass.'>';
		echo '<td><a href="'.$todo[$i]['link'].'">'.$todo[$i]['linktext'].'</a></td>';
		echo '<td>'.$todo[$i]['msg'].'</a></td>';
		echo '</tr>';
	}
	echo '</table>';
}

// about section
echo'<table width="100%" border="0" cellspacing="1" class="outer">';
echo '<tr><th>'._MI_GWLOTO_ADMENU_ABOUT.'</th></tr><tr><td width="100%" >';
echo '<center><br /><b>'. _MI_GWLOTO_DESC . '</b></center><br />';
echo '<center>Brought to you by <a href="http://www.geekwright.com/" target="_blank">geekwright, LLC</a></center><br />';
echo '</td></tr>';
echo '</table>';


xoops_cp_footer();
?>