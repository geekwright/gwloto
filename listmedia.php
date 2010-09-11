<?php
/**
* listmedia.php - display a list of media items
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
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_editmedia.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');

// leave if we don't have any media authority
if(!isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT]) && 
	!isset($places['currentauth'][_GWLOTO_USERAUTH_MD_VIEW]) && 
	!isset($places['currentauth'][_GWLOTO_USERAUTH_MD_TRANS])) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

// check for select mode on clipboard
	$caption='';
	$returnurl='';
	$attachextra='';
	$attach_type=$places['userinfo']['clipboard_type'];
	$generic_id=$places['userinfo']['clipboard_id'];

	switch($attach_type) {
		case 'mediaattach_place':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_PLACE,getPlaceName($generic_id, $language));
			$returnurl="index.php?pid=$generic_id";
			$attachextra="pid=$generic_id";
			break;
		case 'mediaattach_plan':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_PLAN,getCplanName($generic_id, $language));
			$returnurl="viewplan.php?cpid=$generic_id";
			$attachextra="cpid=$generic_id";
			break;
		case 'mediaattach_point':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_POINT,getCpointName($generic_id, $language));
			$returnurl="viewpoint.php?ptid=$generic_id";
			$attachextra="ptid=$generic_id";
			break;
		case 'mediaattach_job':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_JOB,getJobName($generic_id,$language));
			$returnurl="viewjob.php?jid=$generic_id";
			$attachextra="jid=$generic_id";
			break;
		case 'mediaattach_jobstep':
			$caption=sprintf(_MD_GWLOTO_MEDIA_SELECT_PROMPT,_MD_GWLOTO_MEDIA_SELECT_TYPE_JOBSTEP,getJobStepName($generic_id,$language));
			$returnurl="viewstep.php?jsid=$generic_id";
			$attachextra="jsid=$generic_id";
			break;
	}
	if($caption!='') $xoopsTpl->assign('media_mode_select',$caption);

// cancel button
	if(isset($_POST['media_cancel_button'])) {
		setClipboard($myuserid);
		if($returnurl==='') $returnurl='index.php';
		redirect_header($returnurl, 3,_MD_GWLOTO_MEDIA_SELECT_CANCELED);
	}
// attach button
	if(isset($_POST['media_select_button']) && isset($_POST['mid'])) {
		$cleanredir="attachmedia.php?mid=$currentmedia&$attachextra";
		$dirname=$xoopsModule->getInfo('dirname');
		header('Location: ' . XOOPS_URL . '/modules/'.$dirname.'/'. $cleanredir);
		exit;
	}

// build an IN() clause from out current place chain
	$inclause='';
	foreach($places['chaindown'] as $v) {
		if($inclause!='') $inclause.=',';
		$inclause.="$v";
	}

// searching
$mediasearchterms='';
$media_class='';
$myts = myTextSanitizer::getInstance();
if(isset($_GET['mediasearchterms'])) $mediasearchterms = cleaner($_GET['mediasearchterms']);
if(isset($_POST['mediasearchterms'])) $mediasearchterms = cleaner($_POST['mediasearchterms']);
$xoopsTpl->assign('mediasearchterms',$mediasearchterms);
$mediasearchterms=$myts->addslashes($mediasearchterms);
if(isset($_GET['media_class'])) $media_class = cleaner($_GET['media_class']);
if(isset($_POST['media_class'])) $media_class = cleaner($_POST['media_class']);
$xoopsTpl->assign('media_class',$media_class);
$media_class=$myts->addslashes($media_class);

$xoopsTpl->assign('mediaclass',$mediaclass);  // for select list

if($mediasearchterms!='') {
	$searchsql=" AND (media_name like '%$mediasearchterms%' OR media_description like '%$mediasearchterms%') ";
}
else $searchsql='';
if($media_class!='') {
	$searchsql.=" AND media_class = '$media_class' ";
}	
// set up pagenav
	$start=0;
	$limit=20;
	if(isset($_GET['start'])) $start = intval($_GET['start']);

	$sql='SELECT COUNT(*) FROM '. $xoopsDB->prefix('gwloto_media').', '.$xoopsDB->prefix('gwloto_media_detail');
	$sql.= " WHERE media_auth_place in ($inclause) AND (language_id=$language OR language_id=0) ";
	$sql.= ' AND media = media_id ';
	$sql.=$searchsql;
	$total=0;
	$result = $xoopsDB->query($sql);
	if ($result) {
		$myrow=$xoopsDB->fetchRow($result);
		$total=$myrow[0];
	}

	if ($total > $limit) {
	    include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
		$extranav="pid=$currentplace";
		if($mediasearchterms!='') $extranav.="&mediasearchterms=$mediasearchterms";
		if($media_class!='') $extranav.="&media_class=$media_class";
	    $nav = new xoopsPageNav($total,$limit,$start,'start',$extranav);
	    $xoopsTpl->assign('pagenav', $nav->renderNav());
	}

// build an array of media items
	$media=array();

	$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_media').', '.$xoopsDB->prefix('gwloto_media_detail');
	$sql.= " WHERE media_auth_place in ($inclause) AND (language_id=$language OR ";
	$sql.="(language_id=0 AND NOT EXISTS(SELECT * FROM ".$xoopsDB->prefix('gwloto_media_detail')." WHERE media = media_id AND language_id = $language)))";
	$sql.= ' AND media = media_id ';
	$sql.=$searchsql;
	$sql.= ' ORDER BY media_name ';

	$result = $xoopsDB->query($sql,$limit,$start);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$media[$myrow['media_id']] = $myrow;
			$media[$myrow['media_id']]['display_media_class'] = $mediaclass[$myrow['media_class']];
		}
	}

	if(count($media)==0) {
		if($searchsql=='') $err_message=_MD_GWLOTO_MEDIA_NO_MEDIA;
		else $err_message=_MD_GWLOTO_MEDIA_NO_MATCH;
	}
	$xoopsTpl->assign('medialist', $media);

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//$debug.='<pre>$media='.print_r($media,true).'</pre>';

setPageTitle(_MD_GWLOTO_TITLE_LISTMEDIA);

$xoopsTpl->assign('crumburl','');
//$xoopsTpl->assign('crumbextra','&uid='.$auth_uid);
$xoopsTpl->assign('crumbpcurl','');

if(isset($actions)) $xoopsTpl->assign('actions', $actions);
if(isset($body)) $xoopsTpl->assign('body', $body);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
