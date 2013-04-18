<?php
/**
* editplugin.php - edit or translate plugin descriptions
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

// leave if we don't have admin authority
if($xoopsUser && $xoopsUser->isAdmin()) {
	$op="ok";
}
else {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

$op='display';
if(isset($_POST['submit'])) {
	$op='update';
}
if(isset($_POST['lchange'])) {
	$op='display';
}
/*
CREATE TABLE gwloto_plugin_register (
  plugin_id int(8) unsigned NOT NULL auto_increment,
  plugin_type ENUM('jobprint','unknown') NOT NULL default 'unknown',
  plugin_seq int(8) NOT NULL,
  plugin_link varchar(255) NOT NULL,
  plugin_filename varchar(255) NOT NULL,
  plugin_language_filename varchar(255) NOT NULL,
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (plugin_id),
  UNIQUE KEY (plugin_type, plugin_seq, plugin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_plugin_name (
  plugin int(8) unsigned NOT NULL,
  language_id tinyint unsigned NOT NULL default '0',
  plugin_name varchar(255) NOT NULL,
  plugin_description text NOT NULL,
  PRIMARY KEY (plugin, language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/
$plugin_id=0;
if(isset($_GET['plugin_id'])) $plugin_id=intval($_GET['plugin_id']);
if(isset($_POST['plugin_id'])) $plugin_id=intval($_POST['plugin_id']);

$plugin_type='';
$plugin_link='';
$plugin_filename='';
$plugin_language_filename='';
$plugin_name='';
$plugin_description='';


// get data from table

	global $xoopsDB;
	$reports=array();

	$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_plugin_register').', '.$xoopsDB->prefix('gwloto_plugin_name');
	$sql.= " WHERE plugin_id = $plugin_id AND (language_id=$language OR language_id=0) ";
	$sql.= ' AND plugin = plugin_id ';
	$sql.= ' ORDER BY language_id ';
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$plugin_id=$myrow['plugin_id'];
			$plugin_type=$myrow['plugin_type'];
			$plugin_link=$myrow['plugin_link'];
			$plugin_filename=$myrow['plugin_filename'];
			$plugin_language_filename=$myrow['plugin_language_filename'];
			$plugin_name=$myrow['plugin_name'];
			$plugin_description=$myrow['plugin_description'];
		}
	}
	else {
		redirect_header('index.php', 3, _MD_GWLOTO_EDITPLACE_NOTFOUND);
	}

// apply inputs
if ($op!='display') {
	if(isset($_POST['plugin_name'])) $plugin_name=cleaner($_POST['plugin_name']);
	if(isset($_POST['plugin_description'])) $plugin_description=cleaner($_POST['plugin_description']);

	$check=$GLOBALS['xoopsSecurity']->check();

	if (!$check) {
		$op='display';
		$err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
	}
}

if($op=='update') {
	$sl_plugin_name=dbescape($plugin_name);
	$sl_plugin_description=dbescape($plugin_description);
	$sl_place_required_ppe=dbescape($place_required_ppe);

	$sql ="UPDATE ".$xoopsDB->prefix('gwloto_plugin_name');
	$sql.=" SET plugin_name = '$sl_plugin_name'";
	$sql.=" , plugin_description = '$sl_plugin_description' ";
	$sql.=" WHERE plugin = $plugin_id and language_id=$language ";

	$result = $xoopsDB->queryF($sql);
	if ($result) {
		$rcnt=$xoopsDB->getAffectedRows();
		if($rcnt==0) {
		$sql ="INSERT INTO ".$xoopsDB->prefix('gwloto_plugin_name');
			$sql.=" (plugin,language_id, plugin_name, plugin_description)";
			$sql.=" VALUES ($plugin_id, $language, '$sl_plugin_name', '$sl_plugin_description' )";
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

	$form = new XoopsThemeForm(_MD_GWLOTO_TITLE_EDITPLUGIN, 'form1', 'editplugin.php', 'POST', $token);

	$caption = _MD_GWLOTO_PLUGIN_NAME;
	$form->addElement(new XoopsFormText($caption, 'plugin_name', 40, 250, htmlspecialchars($plugin_name, ENT_QUOTES)),true);

	$caption = _MD_GWLOTO_PLUGIN_DESCRIPTION;
	$form->addElement(new XoopsFormTextArea($caption, 'plugin_description', $plugin_description, 15, 50, 'plugin_description'),false);

	$form->addElement(new XoopsFormHidden('plugin_id', $plugin_id));

	$form->addElement(new XoopsFormButton(_MD_GWLOTO_EDITPLACE_UPDATE, 'submit', _MD_GWLOTO_EDITPLACE_UPDATE_BUTTON, 'submit'));

	// translator functionality

	$available_languages=getLanguages();

	$caption = _MD_GWLOTO_LANG_TRAY;

	$langtray=new XoopsFormElementTray($caption, '');

	$listbox = new XoopsFormSelect('', 'lid', $language, 1, false);
	foreach ($available_languages as $i => $v) {
		$listbox->addOption($i, $v);
	}
	$langtray->addElement($listbox);

	$langtray->addElement(new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_CHANGE_BUTTON, 'submit'));

		$googleTranslateEnabled=($xoopsModuleConfig['enable_translate']==1);
		$bingTranslateEnabled=($xoopsModuleConfig['enable_translate']==2);
		$TranslateAPIKey=$xoopsModuleConfig['translate_api_key'];

		$langcodes=getLanguageCodes();
		foreach($langcodes as $i => $v) {
			if($v=='') { $googleTranslateEnabled=false; $bingTranslateEnabled=false; }
		}

		if($googleTranslateEnabled) {
			if($TranslateAPIKey=='') { $key=''; }
			else { $key='key='.$TranslateAPIKey; }
			$xoTheme->addScript('http://www.google.com/jsapi?'.$key); 

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

function doTranslate(form) {
	var langId = form.lid.value;

	var content = new Object;
	content.type = 'text';
	content.text = form.plugin_name.value;
	if(content.text.length>0) {
		google.language.translate(content, "", langCodes[langId], function(result) {
			if (!result.error) {
				form.plugin_name.value=result.translation;
			} } );
	}

	var content = new Object;
	content.type = 'text';
	content.text = form.plugin_description.value;
	if(content.text.length>0) {
		google.language.translate(content, "", langCodes[langId], function(result) {
			if (!result.error) {
				form.plugin_description.value=result.translation;
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

// begin microsoft translate support
		if($bingTranslateEnabled) {
			if($TranslateAPIKey=='') $bingTranslateEnabled=false;
		}
		if($bingTranslateEnabled) {
			$translate_js = '';
			$langcodes=getLanguageCodes();
			$translate_js .= 'var langCodes=new Array();';
			foreach($langcodes as $i => $v) {
				$translate_js .= "langCodes[$i]=\"$v\";";
			}
			$translate_js .= "appIdToken=\"$TranslateAPIKey\";";

			$translate_js .= <<<ENDJSCODEB

function prepInput(value) {
	var preped = value.replace(/\\n/g, "<br />");
	preped = preped.replace(/"/g, "&quot;");
	preped = encodeURIComponent(preped);
	return preped;
}

function prepOutput(value) {
	var preped = decodeURIComponent(value);
	preped = preped.replace(/<br\s*\/?>/g, "\\n");
	preped = preped.replace(/&quot;/g, "\\"");
	return preped;
}

var Translate={
	baseUrl:"http://api.microsofttranslator.com/V2/Ajax.svc/",
	appId:appIdToken,
	contentType:"text/html",
	translate:function(text,from,to,callback){
		if(text.length>0) {
			var s = document.createElement("script");
			s.src =this.baseUrl+"/Translate";
			s.src += "?oncomplete=" + callback; 
			s.src += "&appId=" + this.appId;
			s.src += "&from" + from;
			s.src += "&to=" + to;
			s.src += "&contentType=" + this.contentType;
			s.src += "&text=" + prepInput(text); 
			document.getElementsByTagName("head")[0].appendChild(s);
		}
	}
}

var cb_plugin_name=function(result){
	var form = window.document.form1;
	form.plugin_name.value=prepOutput(result);
};

var cb_plugin_description=function(result){
	var form = window.document.form1;
	form.plugin_description.value=prepOutput(result);
};

function doTranslate(form) {
	var langId = form.lid.value;

	var langFrom = ''; // autodetect
	var langTo = langCodes[langId];

	Translate.translate(form.plugin_name.value,langFrom,langTo,"cb_plugin_name");
	Translate.translate(form.plugin_description.value,langFrom,langTo,"cb_plugin_description");

}
ENDJSCODEB;

			$xoTheme->addScript( null, array( 'type' => 'text/javascript' ), $translate_js );

			$translate_button=new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_TRANS_BUTTON, 'button');
			$translate_button->setExtra(' onClick=\'doTranslate(this.form)\' ');
			$langtray->addElement($translate_button);
		}

// end microsoft translate support

	$form->addElement($langtray);


	//$form->display();
	$body.=$form->render();

$dirname=$xoopsModule->getInfo('dirname');
$body.='<br /><a href="'.XOOPS_URL.'/modules/'.$dirname.'/admin/plugins.php">'._MD_GWLOTO_PLUGIN_ADMIN.'</a>';

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

if(isset($body)) $xoopsTpl->assign('body', $body);

setPageTitle(_MD_GWLOTO_TITLE_EDITPLUGIN);

if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
