<?php
/**
* editplace.php - edit place detail and translations
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

if(!isset($currentplace) && !isset($places['choose'])) {
	if($myuserid==0) {
		$err_message = _MD_GWLOTO_MSG_ANON_ACCESS;
	} else {
		$err_message = _MD_GWLOTO_MSG_NO_ACCESS;
	}
}

// leave if we don't have place administrator authority
if(!isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT]) && !isset($places['currentauth'][_GWLOTO_USERAUTH_PL_TRANS])) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

$op='display';
if(isset($_POST['submit'])) {
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT])) $op='update';
	else {
		if(isset($places['currentauth'][_GWLOTO_USERAUTH_PL_TRANS])) {
			if($language==0) $err_message = _MD_GWLOTO_MSG_NO_TRANSLATE_DEFAULT;
			else  $op='update';
		}
	}
}
// translator functionality
if(isset($places['currentauth'][_GWLOTO_USERAUTH_PL_TRANS])) {
	if(isset($_POST['lchange'])) {
		$op='lchange';
	}
}

$place_name='';
$place_hazard_inventory='';
$place_required_ppe='';

// get data from table

	$sql='SELECT language_id, place_name, place_hazard_inventory, place_required_ppe FROM '.$xoopsDB->prefix('gwloto_place_detail');
	$sql.=" WHERE place = $currentplace and (language_id=$language OR language_id=0)";
	$sql.=' ORDER BY language_id ';

	$cnt=0;
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$displayed_lid=$myrow['language_id'];
			$place_name=$myrow['place_name'];
			$place_hazard_inventory = $myrow['place_hazard_inventory'];
			$place_required_ppe = $myrow['place_required_ppe'];
		}
	}
	else {
		redirect_header('index.php', 3, _MD_GWLOTO_EDITPLACE_NOTFOUND);
	}


// if form was submitted, and not a language change request, use form values instead.
if($op!='lchange') {
	if(isset($_POST['place_name'])) $place_name = cleaner($_POST['place_name']);
	if(isset($_POST['place_hazard_inventory'])) $place_hazard_inventory = cleaner($_POST['place_hazard_inventory']);
	if(isset($_POST['place_required_ppe'])) $place_required_ppe = cleaner($_POST['place_required_ppe']);
}

if ($op!='display') {
	$check=$GLOBALS['xoopsSecurity']->check();

	if (!$check) {
		$op='display';
		$err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
	}
}

if($op=='update') {
	$myts = myTextSanitizer::getInstance();
	$sl_place_name=$myts->addslashes($place_name);
	$sl_place_hazard_inventory=$myts->addslashes($place_hazard_inventory);
	$sl_place_required_ppe=$myts->addslashes($place_required_ppe);

	$sql ="UPDATE ".$xoopsDB->prefix('gwloto_place_detail');
	$sql.=" SET place_name = '$sl_place_name'";
	$sql.=" , place_hazard_inventory = '$sl_place_hazard_inventory' ";
	$sql.=" , place_required_ppe = '$sl_place_required_ppe' ";
	$sql.=" , last_changed_by = $myuserid ";
	$sql.=" , last_changed_on = UNIX_TIMESTAMP() ";
	$sql.=" WHERE place = $currentplace and language_id=$language ";

	$result = $xoopsDB->queryF($sql);
	if ($result) {
		$rcnt=$xoopsDB->getAffectedRows();
		if($rcnt==0 && ($displayed_lid!=$language && isset($places['currentauth'][_GWLOTO_USERAUTH_PL_TRANS]))) {
			$sql ="INSERT INTO ".$xoopsDB->prefix('gwloto_place_detail');
			$sql.=" (place, language_id, place_name, place_hazard_inventory, place_required_ppe, last_changed_by, last_changed_on)";
			$sql.=" VALUES ($currentplace, $language, '$sl_place_name', '$sl_place_hazard_inventory', '$sl_place_required_ppe', $myuserid, UNIX_TIMESTAMP() )";
			$result = $xoopsDB->queryF($sql);
		}
	}

	if ($result) {
		$message = _MD_GWLOTO_EDITPLACE_UPDATE_OK;
	}
	else {
		$err_message .= _MD_GWLOTO_EDITPLACE_DB_ERROR .' '.formatDBError();
	}
}

	$token=true;

	$form = new XoopsThemeForm(_MD_GWLOTO_EDITPLACE_FORM, 'form1', 'editplace.php', 'POST', $token);

	$caption = _MD_GWLOTO_EDITPLACE_NAME;
	$form->addElement(new XoopsFormText($caption, 'place_name', 40, 250, htmlspecialchars($place_name, ENT_QUOTES)),true);

	$caption = _MD_GWLOTO_EDITPLACE_HAZARDS;
	$form->addElement(new XoopsFormTextArea($caption, 'place_hazard_inventory', $place_hazard_inventory, 15, 50, 'place_hazard_inventory'),false);

	$caption = _MD_GWLOTO_EDITPLACE_PPE;
	$form->addElement(new XoopsFormTextArea($caption, 'place_required_ppe', $place_required_ppe, 10, 50, 'place_required_ppe'),false);

	$form->addElement(new XoopsFormHidden('pid', $currentplace));

	$form->addElement(new XoopsFormButton(_MD_GWLOTO_EDITPLACE_UPDATE, 'submit', _MD_GWLOTO_EDITPLACE_UPDATE_BUTTON, 'submit'));

	// translator functionality
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_PL_TRANS])) {
		$available_languages=getLanguages();

		$caption = _MD_GWLOTO_LANG_TRAY;

		$langtray=new XoopsFormElementTray($caption, '');

		$listbox = new XoopsFormSelect('', 'lid', $language, 1, false);
		foreach ($available_languages as $i => $v) {
			$listbox->addOption($i, $v);
		}
		$langtray->addElement($listbox);

		$langtray->addElement(new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_CHANGE_BUTTON, 'submit'));

		$googleTranslateEnabled=$xoopsModuleConfig['enable_google'];
		$langcodes=getLanguageCodes();
		foreach($langcodes as $i => $v) {
			if($v=='') $googleTranslateEnabled=false;
		}

		if($googleTranslateEnabled) {
			$xoTheme->addScript('http://www.google.com/jsapi');

			$translate_js = '';
			$langcodes=getLanguageCodes();
			$translate_js .= 'var langCodes=new Array();';
			foreach($langcodes as $i => $v) {
				$translate_js .= "langCodes[$i]=\"$v\";";
			}

			$translate_js .= <<<ENDJSCODE

google.load("language", "1");
google.setOnLoadCallback(googleinit);

function googleinit() {
	google.language.getBranding("googlebranding");
}

function nl2br(value) {
	return value.replace(/\\n/g, "<br />");
}

function br2nl(value) {
	return value.replace(/<br\s*\/?> /g, "\\n");
}

function doTranslate(form) {
	var langId = form.lid.value;

	var content = new Object;
	content.type = 'text';
	content.text = form.place_name.value;
	if(content.text.length>0) {
		google.language.translate(content, "", langCodes[langId], function(result) {
			if (!result.error) {
				form.place_name.value=result.translation;
			} } );
	}

	var content = new Object;
	content.type = 'text';
	content.text = form.place_hazard_inventory.value;
	if(content.text.length>0) {
		google.language.translate(content, "", langCodes[langId], function(result) {
			if (!result.error) {
				form.place_hazard_inventory.value=br2nl(result.translation);
			} } );
	}

	var content = new Object;
	content.type = 'text';
	content.text = form.place_required_ppe.value;
	if(content.text.length>0) {
		google.language.translate(content, "", langCodes[langId], function(result) {
			if (!result.error) {
				form.place_required_ppe.value=result.translation;
			} } );
	}
}
ENDJSCODE;

			$xoTheme->addScript( null, array( 'type' => 'text/javascript' ), $translate_js );

			$translate_button=new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_TRANS_BUTTON, 'button');
			$translate_button->setExtra(' onClick=\'doTranslate(this.form)\' ');
			$langtray->addElement($translate_button);
			$langtray->addElement(new XoopsFormLabel('', '<span id=\'googlebranding\'> </span>', 'branding'),false);
		}

		$form->addElement($langtray);
	}


	//$form->display();
	$body=$form->render();

$canedit=isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT]);
$media=getAttachedMedia('place', $currentplace, $language, $canedit);

//$debug="op=$op  language=$language displayed_lid=$displayed_lid <br />";
//$debug.='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

if(isset($body)) $xoopsTpl->assign('body', $body);

setPageTitle(_MD_GWLOTO_TITLE_EDITPLACE);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
