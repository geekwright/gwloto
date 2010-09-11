<?php
/**
* newpoint.php - add a new control point to a control plan
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
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_editpoint.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/userauth.php');
include ('include/userauthlist.php');
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');

// leave if we don't have control plan edit authority
if(!isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

if(!isset($currentplan)) {
	redirect_header('index.php', 3, _MD_GWLOTO_EDITPLAN_NOTFOUND);
}

$op='display';
if(isset($_POST['submit'])) {
	$op='add';
}

$cpoint_name='';
$disconnect_instructions='';
$disconnect_state='';
$locks_required=1;
$tags_required=1;
$reconnect_instructions='';
$reconnect_state='';
$inspection_instructions='';


	if(isset($_POST['cpoint_name'])) $cpoint_name = cleaner($_POST['cpoint_name']);
	if(isset($_POST['disconnect_instructions'])) $disconnect_instructions = cleaner($_POST['disconnect_instructions']);
	if(isset($_POST['disconnect_state'])) $disconnect_state = cleaner($_POST['disconnect_state']);
	if(isset($_POST['locks_required'])) $locks_required = intval($_POST['locks_required']);
	if(isset($_POST['tags_required'])) $tags_required = intval($_POST['tags_required']);
	if(isset($_POST['reconnect_instructions'])) $reconnect_instructions = cleaner($_POST['reconnect_instructions']);
	if(isset($_POST['reconnect_state'])) $reconnect_state = cleaner($_POST['reconnect_state']);
	if(isset($_POST['inspection_instructions'])) $inspection_instructions = cleaner($_POST['inspection_instructions']);
	if(isset($_POST['inspection_state'])) $inspection_state = cleaner($_POST['inspection_state']);

if ($op!='display') {
	$check=$GLOBALS['xoopsSecurity']->check();

	if (!$check) {
		$op='display';
		$err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
	}
}

if($op=='add') {
	$myts = myTextSanitizer::getInstance();
	$sl_cpoint_name=$myts->addslashes($cpoint_name);
	$sl_disconnect_instructions=$myts->addslashes($disconnect_instructions);
	$sl_disconnect_state=$myts->addslashes($disconnect_state);
	$sl_reconnect_instructions=$myts->addslashes($reconnect_instructions);
	$sl_reconnect_state=$myts->addslashes($reconnect_state);
	$sl_inspection_instructions=$myts->addslashes($inspection_instructions);
	$sl_inspection_state=$myts->addslashes($inspection_state);

	$dberr=false;
	$dbmsg='';
	startTransaction();

	$cpointcnt=getCPointCounts($currentplan);
	$nextseq=$cpointcnt['count']+1;

	$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cpoint');
	$sql.=' (cplan_id, seq_disconnect, seq_reconnect, seq_inspection, locks_required, tags_required) ';
	$sql.=" VALUES ($currentplan, $nextseq, $nextseq, $nextseq, $locks_required, $tags_required)";
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$new_point_id = $xoopsDB->getInsertId();
		$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cpoint_detail');
		$sql.=' (cpoint, language_id, cpoint_name, disconnect_instructions, disconnect_state, reconnect_instructions, reconnect_state, inspection_instructions, inspection_state, last_changed_by, last_changed_on) ';
		$sql.=" VALUES ($new_point_id, $language, '$sl_cpoint_name', '$sl_disconnect_instructions', '$sl_disconnect_state', '$sl_reconnect_instructions', '$sl_reconnect_state', '$sl_inspection_instructions', '$sl_inspection_state', $myuserid, UNIX_TIMESTAMP() )";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		commitTransaction();
		$cpoint_name='';
		$disconnect_instructions='';
		$disconnect_state='';
		$locks_required=1;
		$tags_required=1;
		$reconnect_instructions='';
		$reconnect_state='';
		$inspection_instructions='';
		$inspection_state='';
		$message = _MD_GWLOTO_NEWPOINT_ADD_OK;
//		redirect_header("newpoint.php?cpid=$currentplan", 3, $message);
	}
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_NEWPOINT_DB_ERROR .' '.$dbmsg;
	}
}

	$planreqs=getPlanReqs();
	$token=true;

	$formtitle=sprintf(_MD_GWLOTO_NEWPOINT_FORM, getCplanName($currentplan, $language));
	$form = new XoopsThemeForm($formtitle, 'form1', 'newpoint.php', 'POST', $token);

	$caption = _MD_GWLOTO_EDITPOINT_NAME;
	$form->addElement(new XoopsFormText($caption, 'cpoint_name', 40, 250, htmlspecialchars($cpoint_name, ENT_QUOTES)),true);

	$caption = _MD_GWLOTO_EDITPOINT_LOCKS_REQ;
	$listboxl = new XoopsFormSelect($caption, 'locks_required', $locks_required, 1, false);
	for($i=0;$i<=$xoopsModuleConfig['maxtagcopies'];$i++) {
		$listboxl->addOption($i, $i);
	}
	$form->addElement($listboxl,true);

	$caption = _MD_GWLOTO_EDITPOINT_TAGS_REQ;
	$listboxt = new XoopsFormSelect($caption, 'tags_required', $tags_required, 1, false);
	for($i=0;$i<=$xoopsModuleConfig['maxtagcopies'];$i++) {
		$listboxt->addOption($i, $i);
	}
	$form->addElement($listboxt,true);


	$caption = _MD_GWLOTO_EDITPOINT_DISC_STATE;
	$form->addElement(new XoopsFormText($caption, 'disconnect_state', 40, 250, htmlspecialchars($disconnect_state, ENT_QUOTES)),true);

	$caption = _MD_GWLOTO_EDITPOINT_DISC_INST;
	$form->addElement(new XoopsFormTextArea($caption, 'disconnect_instructions', $disconnect_instructions, 10, 50, 'disconnect_instructions'),$planreqs['disconnect_instructions']);

if($xoopsModuleConfig['show_inspect']) {
	$caption = _MD_GWLOTO_EDITPOINT_INSP_STATE;
	$form->addElement(new XoopsFormText($caption, 'inspection_state', 40, 250, htmlspecialchars($inspection_state, ENT_QUOTES)),$planreqs['inspection_state']);

	$caption = _MD_GWLOTO_EDITPOINT_INSP_INST;
	$form->addElement(new XoopsFormTextArea($caption, 'inspection_instructions', $inspection_instructions, 10, 50, 'inspection_instructions'),$planreqs['inspection_instructions']);
}

	$caption = _MD_GWLOTO_EDITPOINT_RECON_STATE;
	$form->addElement(new XoopsFormText($caption, 'reconnect_state', 40, 250, htmlspecialchars($reconnect_state, ENT_QUOTES)),true);

if($xoopsModuleConfig['show_reconnect']) {
	$caption = _MD_GWLOTO_EDITPOINT_RECON_INST;
	$form->addElement(new XoopsFormTextArea($caption, 'reconnect_instructions', $disconnect_instructions, 10, 50, 'reconnect_instructions'),$planreqs['reconnect_instructions']);
}

	$form->addElement(new XoopsFormHidden('cpid', $currentplan));

	$form->addElement(new XoopsFormButton(_MD_GWLOTO_NEWPOINT_ADD_BUTTON_DSC, 'submit', _MD_GWLOTO_NEWPOINT_ADD_BUTTON, 'submit'));

	//$form->display();
	$body=$form->render();

$cpoints=getControlPoints($currentplan, $language, 'seq_disconnect');
if(isset($cpoints)) $xoopsTpl->assign('cpoints', $cpoints);

//$debug="op=$op  language=$language displayed_lid=$displayed_lid <br />";
//$debug.='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

if(isset($body)) $xoopsTpl->assign('body', $body);

setPageTitle(_MD_GWLOTO_TITLE_NEWPOINT);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
