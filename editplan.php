<?php
/**
* editplan.php - edit control plan detail and translations
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
$currentscript=basename(__FILE__) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include('include/userauth.php');
include('include/userauthlist.php');
include('include/common.php');
include('include/placeenv.php');
include('include/actionmenu.php');

if (!isset($currentplace) && !isset($places['choose'])) {
    if ($myuserid==0) {
        $err_message = _MD_GWLOTO_MSG_ANON_ACCESS;
    } else {
        $err_message = _MD_GWLOTO_MSG_NO_ACCESS;
    }
}

// leave if we don't have control plan edit authority
if (!isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) && !isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
    redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

$op='display';
if (isset($_POST['submit'])) {
    if (isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
        $op='update';
    } else {
        if (isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
            if ($language==0) {
                $err_message = _MD_GWLOTO_MSG_NO_TRANSLATE_DEFAULT;
            } else {
                $op='update';
            }
        }
    }
}
// translator functionality
if (isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
    if (isset($_POST['lchange'])) {
        $op='lchange';
    }
}

$cplan_id=$currentplan;

$cplan_review='';
$hazard_inventory='';
$required_ppe='';
$authorized_personnel='';
$additional_requirements='';
// get data from table

    $sql='SELECT * FROM '.$xoopsDB->prefix('gwloto_cplan_detail');
    $sql.=" WHERE cplan = $cplan_id and (language_id=$language OR language_id=0)";
    $sql.=' ORDER BY language_id ';

    $cnt=0;
    $result = $xoopsDB->query($sql);
    if ($result) {
        while ($myrow=$xoopsDB->fetchArray($result)) {
            $displayed_lid=$myrow['language_id'];
            // $cplan_id=$myrow['cplan'];
            $cplan_name=$myrow['cplan_name'];
            $cplan_review=$myrow['cplan_review'];
            $hazard_inventory = $myrow['hazard_inventory'];
            $required_ppe = $myrow['required_ppe'];
            $authorized_personnel = $myrow['authorized_personnel'];
            $additional_requirements = $myrow['additional_requirements'];
        }
    } else {
        $err_message = _MD_GWLOTO_EDITPLAN_NOTFOUND;
        redirect_header('index.php', 3, $err_message);
    }


// if form was submitted, and not a language change request, use form values instead.
if ($op!='lchange') {
    if (isset($_POST['cplan_name'])) {
        $cplan_name = cleaner($_POST['cplan_name']);
    }
    if (isset($_POST['cplan_review'])) {
        $cplan_review = cleaner($_POST['cplan_review']);
    }
    if (isset($_POST['hazard_inventory'])) {
        $hazard_inventory = cleaner($_POST['hazard_inventory']);
    }
    if (isset($_POST['required_ppe'])) {
        $required_ppe = cleaner($_POST['required_ppe']);
    }
    if (isset($_POST['authorized_personnel'])) {
        $authorized_personnel = cleaner($_POST['authorized_personnel']);
    }
    if (isset($_POST['additional_requirements'])) {
        $additional_requirements = cleaner($_POST['additional_requirements']);
    }
}

if ($op!='display') {
    $check=$GLOBALS['xoopsSecurity']->check();

    if (!$check) {
        $op='display';
        $err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
    }
}

if ($op=='update') {
    $sl_cplan_name=dbescape($cplan_name);
    $sl_cplan_review=dbescape($cplan_review);
    $sl_hazard_inventory=dbescape($hazard_inventory);
    $sl_required_ppe=dbescape($required_ppe);
    $sl_authorized_personnel=dbescape($authorized_personnel);
    $sl_additional_requirements=dbescape($additional_requirements);

    $sql ="UPDATE ".$xoopsDB->prefix('gwloto_cplan_detail');
    $sql.=" SET cplan_name = '$sl_cplan_name'";
    $sql.=" , cplan_review = '$sl_cplan_review'";
    $sql.=" , hazard_inventory = '$sl_hazard_inventory' ";
    $sql.=" , required_ppe = '$sl_required_ppe' ";
    $sql.=" , authorized_personnel = '$sl_authorized_personnel' ";
    $sql.=" , additional_requirements = '$sl_additional_requirements' ";
    $sql.=" , last_changed_by = $myuserid ";
    $sql.=" , last_changed_on = UNIX_TIMESTAMP() ";
    $sql.=" WHERE cplan = $cplan_id and language_id=$language ";

    $result = $xoopsDB->queryF($sql);
    if ($result) {
        $rcnt=$xoopsDB->getAffectedRows();
        if ($rcnt==0 && ($displayed_lid!=$language && isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS]))) {
            $sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cplan_detail');
            $sql.=' (cplan, language_id, cplan_name, cplan_review, hazard_inventory, required_ppe, authorized_personnel, additional_requirements, last_changed_by, last_changed_on) ';
            $sql.=" VALUES ($cplan_id, $language, '$sl_cplan_name', '$sl_cplan_review', '$sl_hazard_inventory', '$sl_required_ppe', '$sl_authorized_personnel', '$sl_additional_requirements', $myuserid, UNIX_TIMESTAMP() )";
            $result = $xoopsDB->queryF($sql);
        }
    }

    if ($result) {
        $message = _MD_GWLOTO_EDITPLAN_UPDATE_OK;
    } else {
        $err_message .= _MD_GWLOTO_EDITPLAN_DB_ERROR .' '.formatDBError();
    }
}

    $planreqs=getPlanReqs();
    $token=true;

    $form = new XoopsThemeForm(_MD_GWLOTO_EDITPLAN_FORM, 'form1', 'editplan.php', 'POST', $token);

    $caption = _MD_GWLOTO_EDITPLAN_NAME;
    $form->addElement(new XoopsFormText($caption, 'cplan_name', 40, 250, htmlspecialchars($cplan_name, ENT_QUOTES)), true);

    $caption = _MD_GWLOTO_EDITPLAN_REVIEW;
    $form->addElement(new XoopsFormTextArea($caption, 'cplan_review', $cplan_review, 10, 50, 'cplan_review'), $planreqs['review']);

    $caption = _MD_GWLOTO_EDITPLAN_HAZARDS;
    $form->addElement(new XoopsFormTextArea($caption, 'hazard_inventory', $hazard_inventory, 10, 50, 'hazard_inventory'), $planreqs['hazard_inventory']);

    $caption = _MD_GWLOTO_EDITPLAN_PPE;
    $form->addElement(new XoopsFormTextArea($caption, 'required_ppe', $required_ppe, 10, 50, 'required_ppe'), $planreqs['required_ppe']);

    $caption = _MD_GWLOTO_EDITPLAN_AUTHPERSONNEL;
    $form->addElement(new XoopsFormTextArea($caption, 'authorized_personnel', $authorized_personnel, 10, 50, 'authorized_personnel'), $planreqs['authorized_personnel']);

    $caption = _MD_GWLOTO_EDITPLAN_ADDREQ;
    $form->addElement(new XoopsFormTextArea($caption, 'additional_requirements', $additional_requirements, 10, 50, 'additional_requirements'), $planreqs['additional_requirements']);

    $form->addElement(new XoopsFormHidden('cpid', $cplan_id));

    $form->addElement(new XoopsFormButton(_MD_GWLOTO_EDITPLAN_UPDATE, 'submit', _MD_GWLOTO_EDITPLAN_UPDATE_BUTTON, 'submit'));

    // translator functionality
    if (isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
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
        foreach ($langcodes as $i => $v) {
            if ($v=='') {
                $googleTranslateEnabled=false;
                $bingTranslateEnabled=false;
            }
        }

        if ($googleTranslateEnabled) {
            if ($TranslateAPIKey=='') {
                $key='';
            } else {
                $key='key='.$TranslateAPIKey;
            }
            $xoTheme->addScript('http://www.google.com/jsapi?'.$key);

            $translate_js = '';
            $langcodes=getLanguageCodes();
            $translate_js .= 'var langCodes=new Array();';
            foreach ($langcodes as $i => $v) {
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
    content.text = form.cplan_name.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.cplan_name.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.cplan_review.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.cplan_review.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.hazard_inventory.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.hazard_inventory.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.required_ppe.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.required_ppe.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.authorized_personnel.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.authorized_personnel.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.additional_requirements.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.additional_requirements.value=result.translation;
            } } );
    }
}
ENDJSCODE;

            $xoTheme->addScript(null, array( 'type' => 'text/javascript' ), $translate_js);

            $translate_button=new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_TRANS_BUTTON, 'button');
            $translate_button->setExtra(' onClick=\'doTranslate(this.form)\' ');
            $langtray->addElement($translate_button);
            $langtray->addElement(new XoopsFormLabel('', '<span id=\'googlebranding\'> </span>', 'branding'), false);
        }

// begin microsoft translate support
        if ($bingTranslateEnabled) {
            if ($TranslateAPIKey=='') {
                $bingTranslateEnabled=false;
            }
        }
        if ($bingTranslateEnabled) {
            $translate_js = '';
            $langcodes=getLanguageCodes();
            $translate_js .= 'var langCodes=new Array();';
            foreach ($langcodes as $i => $v) {
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

var cb_cplan_name=function(result){
    var form = window.document.form1;
    form.cplan_name.value=prepOutput(result);
};

var cb_cplan_review=function(result){
    var form = window.document.form1;
    form.cplan_review.value=prepOutput(result);
};

var cb_hazard_inventory=function(result){
    var form = window.document.form1;
    form.hazard_inventory.value=prepOutput(result);
};

var cb_required_ppe=function(result){
    var form = window.document.form1;
    form.required_ppe.value=prepOutput(result);
};

var cb_authorized_personnel=function(result){
    var form = window.document.form1;
    form.authorized_personnel.value=prepOutput(result);
};

var cb_additional_requirements=function(result){
    var form = window.document.form1;
    form.additional_requirements.value=prepOutput(result);
};

function doTranslate(form) {
    var langId = form.lid.value;

    var langFrom = ''; // autodetect
    var langTo = langCodes[langId];

    Translate.translate(form.cplan_name.value,langFrom,langTo,"cb_cplan_name");
    Translate.translate(form.cplan_review.value,langFrom,langTo,"cb_cplan_review");
    Translate.translate(form.hazard_inventory.value,langFrom,langTo,"cb_hazard_inventory");
    Translate.translate(form.required_ppe.value,langFrom,langTo,"cb_required_ppe");
    Translate.translate(form.authorized_personnel.value,langFrom,langTo,"cb_authorized_personnel");
    Translate.translate(form.additional_requirements.value,langFrom,langTo,"cb_additional_requirements");

}
ENDJSCODEB;

            $xoTheme->addScript(null, array( 'type' => 'text/javascript' ), $translate_js);

            $translate_button=new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_TRANS_BUTTON, 'button');
            $translate_button->setExtra(' onClick=\'doTranslate(this.form)\' ');
            $langtray->addElement($translate_button);
        }

// end microsoft translate support

        $form->addElement($langtray);

        $transtats=getCPointTranslateStats($cplan_id);
        $xoopsTpl->assign('transtats', $transtats);
    }

    //$form->display();
    $body=$form->render();

//$debug="op=$op  language=$language displayed_lid=$displayed_lid <br />";
//$debug.='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

$cpoints=getControlPoints($cplan_id, $language, 'seq_disconnect');
if (isset($cpoints)) {
    $xoopsTpl->assign('cpoints', $cpoints);
}

$canedit=isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]);
$media=getAttachedMedia('plan', $currentplan, $language, $canedit);

setPageTitle(_MD_GWLOTO_TITLE_EDITPLAN);

$cpoints=getControlPoints($currentplan, $language, 'seq_disconnect');
if (isset($cpoints)) {
    $xoopsTpl->assign('cpoints', $cpoints);
}

if (isset($body)) {
    $xoopsTpl->assign('body', $body);
}

if (isset($places['choose'])) {
    $xoopsTpl->assign('choose', $places['choose']);
}
if (isset($places['crumbs'])) {
    $xoopsTpl->assign('crumbs', $places['crumbs']);
}

if (isset($message)) {
    $xoopsTpl->assign('message', $message);
}
if (isset($err_message)) {
    $xoopsTpl->assign('err_message', $err_message);
}
if (isset($debug)) {
    $xoopsTpl->assign('debug', $debug);
}

include(XOOPS_ROOT_PATH.'/footer.php');
