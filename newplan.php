<?php
/**
* newplan.php - add a new control plan
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

$op='display';
if(isset($_POST['submit'])) {
	$op='add';
}

$cplan_name='';
$cplan_review='';
$hazard_inventory='';
$required_ppe='';

	if(isset($_POST['cplan_name'])) $cplan_name = cleaner($_POST['cplan_name']);
	if(isset($_POST['cplan_review'])) $cplan_review = cleaner($_POST['cplan_review']);
	if(isset($_POST['hazard_inventory'])) $hazard_inventory = cleaner($_POST['hazard_inventory']);
	if(isset($_POST['required_ppe'])) $required_ppe = cleaner($_POST['required_ppe']);
	if(isset($_POST['authorized_personnel'])) $authorized_personnel = cleaner($_POST['authorized_personnel']);
	if(isset($_POST['additional_requirements'])) $additional_requirements = cleaner($_POST['additional_requirements']);

if ($op!='display') {
	$check=$GLOBALS['xoopsSecurity']->check();

	if (!$check) {
		$op='display';
		$err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
	}
}

if($op=='add') {
	$sl_cplan_name=dbescape($cplan_name);
	$sl_cplan_review=dbescape($cplan_review);
	$sl_hazard_inventory=dbescape($hazard_inventory);
	$sl_required_ppe=dbescape($required_ppe);
	$sl_authorized_personnel=dbescape($authorized_personnel);
	$sl_additional_requirements=dbescape($additional_requirements);

	$dberr=false;
	$dbmsg='';
	startTransaction();
	$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cplan');
	$sql.=' (place_id, last_changed_by, last_changed_on) ';
	$sql.=" VALUES ($currentplace, $myuserid, UNIX_TIMESTAMP() )";
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$new_plan_id = $xoopsDB->getInsertId();
		$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cplan_detail');
		$sql.=' (cplan, language_id, cplan_name, cplan_review, hazard_inventory, required_ppe, authorized_personnel, additional_requirements, last_changed_by, last_changed_on) ';
		$sql.=" VALUES ($new_plan_id, 0, '$sl_cplan_name', '$sl_cplan_review', '$sl_hazard_inventory', '$sl_required_ppe', '$sl_authorized_personnel', '$sl_additional_requirements', $myuserid, UNIX_TIMESTAMP() )";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		commitTransaction();
		$message = _MD_GWLOTO_NEWPLAN_ADD_OK;
		redirect_header("editplan.php?cpid=$new_plan_id", 3, $message);
	}
	else {
		rollbackTransaction();
		$err_message = _MD_GWLOTO_NEWPLAN_DB_ERROR .' '.$dbmsg;
	}
}

	$planreqs=getPlanReqs();
	$token=true;

	$formtitle=sprintf(_MD_GWLOTO_NEWPLAN_FORM, getPlaceName($currentplace, $language));
	$form = new XoopsThemeForm($formtitle, 'form1', 'newplan.php', 'POST', $token);

	$caption = _MD_GWLOTO_EDITPLAN_NAME;
	$form->addElement(new XoopsFormText($caption, 'cplan_name', 40, 250, htmlspecialchars($cplan_name, ENT_QUOTES)),true);

	$caption = _MD_GWLOTO_EDITPLAN_REVIEW;
	$form->addElement(new XoopsFormTextArea($caption, 'cplan_review', $cplan_review, 10, 50, 'cplan_review'),$planreqs['review']);

	$caption = _MD_GWLOTO_EDITPLAN_HAZARDS;
	$form->addElement(new XoopsFormTextArea($caption, 'hazard_inventory', $hazard_inventory, 10, 50, 'hazard_inventory'),$planreqs['hazard_inventory']);

	$caption = _MD_GWLOTO_EDITPLAN_PPE;
	$form->addElement(new XoopsFormTextArea($caption, 'required_ppe', $required_ppe, 10, 50, 'required_ppe'),$planreqs['required_ppe']);

	$caption = _MD_GWLOTO_EDITPLAN_AUTHPERSONNEL;
	$form->addElement(new XoopsFormTextArea($caption, 'authorized_personnel', $authorized_personnel, 10, 50, 'authorized_personnel'),$planreqs['authorized_personnel']);

	$caption = _MD_GWLOTO_EDITPLAN_ADDREQ;
	$form->addElement(new XoopsFormTextArea($caption, 'additional_requirements', $additional_requirements, 10, 50, 'additional_requirements'),$planreqs['additional_requirements']);

	$form->addElement(new XoopsFormHidden('pid', $currentplace));

	$form->addElement(new XoopsFormButton(_MD_GWLOTO_NEWPLAN_ADD_BUTTON_DSC, 'submit', _MD_GWLOTO_NEWPLAN_ADD_BUTTON, 'submit'));

	//$form->display();
	$body=$form->render();

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

if(isset($body)) $xoopsTpl->assign('body', $body);

setPageTitle(_MD_GWLOTO_TITLE_NEWPLAN);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
