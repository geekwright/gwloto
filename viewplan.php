<?php
/**
* viewplan.php - view control plan detail
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
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_viewplan.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/userauth.php');
include ('include/userauthlist.php');
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');

include ('include/seqoptions.php');

// leave if we don't have any control plan authority
if(!(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_CP_VIEW]) ||
isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS]))) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

$show_edit=false;
if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || 
isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
$show_edit=true;
}

$op='display';
if(isset($_POST['submit'])) {
	$op='update';
}

// translator functionality
if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
	if(isset($_POST['lchange']) || isset($_GET['lchange'])) {
		$op='lchange';
	}
}

$cplan_id=$currentplan;

$cplan_review='';
$hazard_inventory='';
$required_ppe='';
$authorized_personnel='';
$additional_requirements='';
$last_changed_by='';
$last_changed_on='';
// get data from table

	$sql='SELECT * FROM '.$xoopsDB->prefix('gwloto_cplan_detail');
	$sql.=" WHERE cplan = $cplan_id and (language_id=$language OR language_id=0)";
	$sql.=' ORDER BY language_id ';

	$cnt=0;
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$displayed_lid=$myrow['language_id'];
//			$cplan_id=$myrow['cplan'];
			$cplan_name=$myrow['cplan_name'];
			$cplan_review=$myrow['cplan_review'];
			$hazard_inventory = $myrow['hazard_inventory'];
			$required_ppe = $myrow['required_ppe'];
			$authorized_personnel = $myrow['authorized_personnel'];
			$additional_requirements = $myrow['additional_requirements'];
			$last_changed_by=$myrow['last_changed_by'];
			$last_changed_on=$myrow['last_changed_on'];
		}
	}
	else {
//		$err_message = _MD_GWLOTO_EDITPLAN_NOTFOUND;
		redirect_header('index.php', 3, _MD_GWLOTO_EDITPLAN_NOTFOUND);
	}

	$cpointcnt=getCPointCounts($cplan_id);

	if($hazard_inventory=='') $hazard_inventory=$places['current']['place_hazard_inventory'];
	if($required_ppe=='') $required_ppe=$places['current']['place_required_ppe'];

	$myts = myTextSanitizer::getInstance();
	// $cplan_name=$myts->nl2Br($cplan_name);
	$cplan_review=$myts->nl2Br($cplan_review);
	$hazard_inventory=$myts->nl2Br($hazard_inventory);
	$required_ppe=$myts->nl2Br($required_ppe);
	$authorized_personnel=$myts->nl2Br($authorized_personnel);
	$additional_requirements=$myts->nl2Br($additional_requirements);

	$token=0;

	$form = new XoopsThemeForm(_MD_GWLOTO_VIEWPLAN_FORM, 'formview', 'viewplan.php', 'GET', $token);

	$caption = _MD_GWLOTO_EDITPLAN_NAME;
	$form->addElement(new XoopsFormLabel($caption, $cplan_name, 'cplan_name'),false);

// XoopsFormLabel( [string $caption = ""], [string $value = ""], [ $name = ""])
	$caption = _MD_GWLOTO_EDITPLAN_REVIEW;
	$form->addElement(new XoopsFormLabel($caption, $cplan_review, 'cplan_review'),false);

	$caption = _MD_GWLOTO_EDITPLAN_HAZARDS;
	$form->addElement(new XoopsFormLabel($caption, $hazard_inventory, 'hazard_inventory'),false);

	$caption = _MD_GWLOTO_EDITPLAN_PPE;
	$form->addElement(new XoopsFormLabel($caption, $required_ppe, 'required_ppe'),false);

	$caption = _MD_GWLOTO_EDITPLAN_AUTHPERSONNEL;
	$form->addElement(new XoopsFormLabel($caption, $authorized_personnel, 'authorized_personnel'),false);

	$caption = _MD_GWLOTO_EDITPLAN_ADDREQ;
	$form->addElement(new XoopsFormLabel($caption, $additional_requirements, 'additional_requirements'),false);

	$caption = _MD_GWLOTO_VIEWPLAN_COUNTS;
	$form->addElement(new XoopsFormLabel($caption, sprintf(_MD_GWLOTO_VIEWPLAN_COUNTS_DETAIL,$cpointcnt['count'],$cpointcnt['tags'],$cpointcnt['locks']), 'cpointcnt'),false);

	$member_handler =& xoops_gethandler('member');
	$thisUser =& $member_handler->getUser($last_changed_by);
        if (!is_object($thisUser) || !$thisUser->isActive() ) {
		$user_name=$last_changed_by;
	} else {
		$user_name = $thisUser->getVar('name');
		if($user_name=='') $user_name = $thisUser->getVar('uname');
	}
	$caption = _MD_GWLOTO_LASTCHG_BY;
	$form->addElement(new XoopsFormLabel($caption,$user_name, 'last_changed_by'),false);

	$caption = _MD_GWLOTO_LASTCHG_ON;
	$form->addElement(new XoopsFormLabel($caption,getDisplayDate($last_changed_on), 'last_changed_on'),false);


	$form->addElement(new XoopsFormHidden('cpid', $cplan_id));

// translator functionality
if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
	$available_languages=getLanguages();

	$caption = _MD_GWLOTO_LANG_TRAY;
	$langtray=new XoopsFormElementTray($caption, '');

	$listbox = new XoopsFormSelect('', 'lid', $language, 1, false);
	foreach ($available_languages as $i => $v) {
		$listbox->addOption($i, $v);
	}
	$langtray->addElement($listbox);

	$langtray->addElement(new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_CHANGE_BUTTON, 'submit'));

	$form->addElement($langtray);

	$transtats=getCPointTranslateStats($cplan_id);
	$xoopsTpl->assign('transtats', $transtats);
}

if(count($seqoptions)>1) {
	$caption = _MD_GWLOTO_VIEWPLAN_SEQ;

	$seqtray=new XoopsFormElementTray($caption, '');

	$radio=new XoopsFormRadio('', 'seq', $currentseq, '');

	foreach($seqoptions as $i => $v) {
		$radio->addOption($i, $v['label']);
	}
	$radio->setExtra('onClick="document.formview.submit()" ');
	$seqtray->addElement($radio);
	$subbutton='<noscript>&nbsp;<input type="submit" value="'._MD_GWLOTO_NOSCRIPT_GO.'" /></noscript>';
	$seqtray->addElement(new XoopsFormLabel('', $subbutton),false);
	$form->addElement($seqtray);

}

//	$form->addElement(new XoopsFormButton(_MD_GWLOTO_EDITPLAN_UPDATE, 'submit', _MD_GWLOTO_EDITPLAN_UPDATE_BUTTON, 'submit'));

	//$form->display();
	$body=$form->render();

$cpoints=getControlPoints($cplan_id, $language, $seqoptions[$currentseq]['sort']);
if(isset($cpoints)) $xoopsTpl->assign('cpoints', $cpoints);

$canedit=false; // isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]);
$media=getAttachedMedia('plan', $currentplan, $language, $canedit);

//$debug='';
//$debug.='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//$debug.='<pre>$cpoints='.print_r($cpoints,true).'</pre>';
//$debug.='<pre>$media='.print_r($media,true).'</pre>';

setPageTitle(_MD_GWLOTO_TITLE_VIEWPLAN);

if(isset($body)) $xoopsTpl->assign('body', $body);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
