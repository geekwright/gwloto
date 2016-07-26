<?php
/**
* editpoint.php - edit control point detail and translations
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
                $op='updatetran';
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

$cpoint_id=$currentpoint;

    $seq_disconnect = 0;
    $seq_reconnect = 0;
    $seq_inspection = 0;
    $locks_required = 0;
    $tags_required = 0;
    $cpoint_name = '';
    $disconnect_instructions = '';
    $disconnect_state = '';
    $reconnect_instructions = '';
    $reconnect_state = '';
    $inspection_instructions = '';
    $inspection_state = '';
    $last_changed_by = 0;
    $last_changed_on = 0;

// get data from table

    $sql='SELECT cpoint_id, language_id, seq_disconnect, seq_reconnect, seq_inspection, locks_required, tags_required, cpoint_name, disconnect_instructions, disconnect_state, reconnect_instructions, reconnect_state, inspection_instructions, inspection_state, last_changed_by, last_changed_on FROM '. $xoopsDB->prefix('gwloto_cpoint').', '.$xoopsDB->prefix('gwloto_cpoint_detail');
    $sql.= " WHERE cpoint_id = $cpoint_id AND (language_id=$language OR language_id=0) ";
    $sql.= ' AND cpoint = cpoint_id ';
    $sql.= " ORDER BY language_id ";


    $cnt=0;
    $result = $xoopsDB->query($sql);
    if ($result) {
        while ($myrow=$xoopsDB->fetchArray($result)) {
            $displayed_lid=$myrow['language_id'];
            $cpoint_id = $myrow['cpoint_id'];
            $language_id = $myrow['language_id'];
            $seq_disconnect = $myrow['seq_disconnect'];
            $seq_reconnect = $myrow['seq_reconnect'];
            $seq_inspection = $myrow['seq_inspection'];
            $locks_required = $myrow['locks_required'];
            $tags_required = $myrow['tags_required'];
            $cpoint_name = $myrow['cpoint_name'];
            $disconnect_instructions = $myrow['disconnect_instructions'];
            $disconnect_state = $myrow['disconnect_state'];
            $reconnect_instructions = $myrow['reconnect_instructions'];
            $reconnect_state = $myrow['reconnect_state'];
            $inspection_instructions = $myrow['inspection_instructions'];
            $inspection_state = $myrow['inspection_state'];
            $last_changed_by = $myrow['last_changed_by'];
            $last_changed_on = $myrow['last_changed_on'];
        }
    } else {
        $err_message = _MD_GWLOTO_EDITPOINT_NOTFOUND;
        redirect_header('index.php', 3, $err_message);
    }



// if form was submitted, and not a language change request, use form values instead.
if ($op!='lchange') {
    if (isset($_POST['locks_required'])) {
        $locks_required = intval($_POST['locks_required']);
    }
    if (isset($_POST['tags_required'])) {
        $tags_required = intval($_POST['tags_required']);
    }
    if (isset($_POST['cpoint_name'])) {
        $cpoint_name = cleaner($_POST['cpoint_name']);
    }
    if (isset($_POST['disconnect_state'])) {
        $disconnect_state = cleaner($_POST['disconnect_state']);
    }
    if (isset($_POST['reconnect_state'])) {
        $reconnect_state = cleaner($_POST['reconnect_state']);
    }
    if (isset($_POST['disconnect_instructions'])) {
        $disconnect_instructions = cleaner($_POST['disconnect_instructions']);
    }
    if (isset($_POST['reconnect_instructions'])) {
        $reconnect_instructions = cleaner($_POST['reconnect_instructions']);
    }
    if (isset($_POST['inspection_instructions'])) {
        $inspection_instructions = cleaner($_POST['inspection_instructions']);
    }
    if (isset($_POST['inspection_state'])) {
        $inspection_state = cleaner($_POST['inspection_state']);
    }
}

if ($op!='display') {
    $check=$GLOBALS['xoopsSecurity']->check();

    if (!$check) {
        $op='display';
        $err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
    }
}

if ($op=='update' || $op=='updatetran') {
    $sl_cpoint_name=dbescape($cpoint_name);
    $sl_disconnect_instructions=dbescape($disconnect_instructions);
    $sl_disconnect_state=dbescape($disconnect_state);
    $sl_reconnect_instructions=dbescape($reconnect_instructions);
    $sl_reconnect_state=dbescape($reconnect_state);
    $sl_inspection_instructions=dbescape($inspection_instructions);
    $sl_inspection_state=dbescape($inspection_state);

    $dberr=false;
    $dbmsg='';
    startTransaction();

    if ($op=='update') {
        $sql ="UPDATE ".$xoopsDB->prefix('gwloto_cpoint');
        $sql.=" SET locks_required = $locks_required";
        $sql.=" , tags_required = $tags_required";
        $sql.=" WHERE cpoint_id = $cpoint_id ";
        $result = $xoopsDB->queryF($sql);
        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $dberr=true;
            $dbmsg=formatDBError();
        }
    }
    if (!$dberr) {
        $sql ="UPDATE ".$xoopsDB->prefix('gwloto_cpoint_detail');
        $sql.=" SET cpoint_name = '$sl_cpoint_name'";
        $sql.=" , disconnect_instructions = '$sl_disconnect_instructions' ";
        $sql.=" , disconnect_state = '$sl_disconnect_state' ";
        $sql.=" , reconnect_instructions = '$sl_reconnect_instructions' ";
        $sql.=" , reconnect_state = '$sl_reconnect_state' ";
        $sql.=" , inspection_instructions = '$sl_inspection_instructions' ";
        $sql.=" , inspection_state = '$sl_inspection_state' ";
        $sql.=" , last_changed_by = $myuserid ";
        $sql.=" , last_changed_on = UNIX_TIMESTAMP() ";
        $sql.=" WHERE cpoint = $cpoint_id and language_id=$language ";

        $result = $xoopsDB->queryF($sql);
        if ($result) {
            $rcnt=$xoopsDB->getAffectedRows();
            if ($rcnt==0 && ($displayed_lid!=$language && isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS]))) {
                $sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_cpoint_detail');
                $sql.=' (cpoint, language_id, cpoint_name, disconnect_instructions, disconnect_state, reconnect_instructions, reconnect_state, inspection_instructions, inspection_state, last_changed_by, last_changed_on) ';
                $sql.=" VALUES ($cpoint_id, $language, '$sl_cpoint_name', '$sl_disconnect_instructions', '$sl_disconnect_state', '$sl_reconnect_instructions', '$sl_reconnect_state', '$sl_inspection_instructions', '$sl_inspection_state', $myuserid, UNIX_TIMESTAMP() )";
                $result = $xoopsDB->queryF($sql);
                if (!$result) {
                    $dberr=true;
                    $dbmsg=formatDBError();
                }
            }
        } else {
            $dberr=true;
            $dbmsg=formatDBError();
        }
    }

    if (!$dberr) {
        commitTransaction();
        $message = _MD_GWLOTO_EDITPOINT_UPDATE_OK;
        // redirect_header("editpoint.php?ptid=$currentpoint", 3, $message);
    } else {
        rollbackTransaction();
        $err_message = _MD_GWLOTO_EDITPOINT_DB_ERROR .' '.$dbmsg;
    }
}

    $planreqs=getPlanReqs();
    $token=true;

    $cplan_name=getCplanName($currentplan, $language);

    $caption = _MD_GWLOTO_EDITPOINT_FORM;
    $form = new XoopsThemeForm($caption, 'form1', 'editpoint.php', 'POST', $token);

    $caption = _MD_GWLOTO_EDITPLAN_NAME;
    $form->addElement(new XoopsFormLabel($caption, '<a href="editplan.php?cpid='.$currentplan.'">'.$cplan_name.'</a>', 'cplan_name'), false);

    $caption = _MD_GWLOTO_EDITPOINT_NAME;
    $form->addElement(new XoopsFormText($caption, 'cpoint_name', 40, 250, htmlspecialchars($cpoint_name, ENT_QUOTES)), true);

    $caption = _MD_GWLOTO_EDITPOINT_LOCKS_REQ;
    $listboxl = new XoopsFormSelect($caption, 'locks_required', $locks_required, 1, false);
    for ($i=0;$i<=$xoopsModuleConfig['maxtagcopies'];$i++) {
        $listboxl->addOption($i, $i);
    }
    $form->addElement($listboxl, true);

    $caption = _MD_GWLOTO_EDITPOINT_TAGS_REQ;
    $listboxt = new XoopsFormSelect($caption, 'tags_required', $tags_required, 1, false);
    for ($i=0;$i<=$xoopsModuleConfig['maxtagcopies'];$i++) {
        $listboxt->addOption($i, $i);
    }
    $form->addElement($listboxt, true);

    $caption = _MD_GWLOTO_EDITPOINT_DISC_STATE;
    $form->addElement(new XoopsFormText($caption, 'disconnect_state', 40, 250, htmlspecialchars($disconnect_state, ENT_QUOTES)), true);

    $caption = _MD_GWLOTO_EDITPOINT_DISC_INST;
    $form->addElement(new XoopsFormTextArea($caption, 'disconnect_instructions', $disconnect_instructions, 10, 50, 'disconnect_instructions'), $planreqs['disconnect_instructions']);

if ($xoopsModuleConfig['show_inspect']) {
    $caption = _MD_GWLOTO_EDITPOINT_INSP_STATE;
    $form->addElement(new XoopsFormText($caption, 'inspection_state', 40, 250, htmlspecialchars($inspection_state, ENT_QUOTES)), $planreqs['inspection_state']);

    $caption = _MD_GWLOTO_EDITPOINT_INSP_INST;
    $form->addElement(new XoopsFormTextArea($caption, 'inspection_instructions', $inspection_instructions, 10, 50, 'inspection_instructions'), $planreqs['inspection_instructions']);
}

    $caption = _MD_GWLOTO_EDITPOINT_RECON_STATE;
    $form->addElement(new XoopsFormText($caption, 'reconnect_state', 40, 250, htmlspecialchars($reconnect_state, ENT_QUOTES)), true);

if ($xoopsModuleConfig['show_reconnect']) {
    $caption = _MD_GWLOTO_EDITPOINT_RECON_INST;
    $form->addElement(new XoopsFormTextArea($caption, 'reconnect_instructions', $reconnect_instructions, 10, 50, 'reconnect_instructions'), $planreqs['reconnect_instructions']);
}

    $form->addElement(new XoopsFormHidden('ptid', $currentpoint));

    $form->addElement(new XoopsFormButton(_MD_GWLOTO_EDITPOINT_UPDATE, 'submit', _MD_GWLOTO_EDITPOINT_UPDATE_BUTTON, 'submit'));

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

function doTranslate(form) {
    var langId = form.lid.value;

    var content = new Object;
    content.type = 'text';
    content.text = form.cpoint_name.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.cpoint_name.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.disconnect_instructions.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.disconnect_instructions.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.disconnect_state.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.disconnect_state.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.reconnect_instructions.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.reconnect_instructions.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.reconnect_state.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.reconnect_state.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.inspection_instructions.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.inspection_instructions.value=result.translation;
            } } );
    }

    var content = new Object;
    content.type = 'text';
    content.text = form.inspection_state.value;
    if(content.text.length>0) {
        google.language.translate(content, "", langCodes[langId], function(result) {
            if (!result.error) {
                form.inspection_state.value=result.translation;
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

            $translate_js .= "useReconnect=".$xoopsModuleConfig['show_reconnect'].";";
            $translate_js .= "useInspect=".$xoopsModuleConfig['show_inspect'].";";

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

var cb_cpoint_name=function(result){
    var form = window.document.form1;
    form.cpoint_name.value=prepOutput(result);
};

var cb_disconnect_instructions=function(result){
    var form = window.document.form1;
    form.disconnect_instructions.value=prepOutput(result);
};

var cb_disconnect_state=function(result){
    var form = window.document.form1;
    form.disconnect_state.value=prepOutput(result);
};

var cb_reconnect_instructions=function(result){
    var form = window.document.form1;
    form.reconnect_instructions.value=prepOutput(result);
};

var cb_reconnect_state=function(result){
    var form = window.document.form1;
    form.reconnect_state.value=prepOutput(result);
};

var cb_inspection_instructions=function(result){
    var form = window.document.form1;
    form.inspection_instructions.value=prepOutput(result);
};

var cb_inspection_state=function(result){
    var form = window.document.form1;
    form.inspection_state.value=prepOutput(result);
};


function doTranslate(form) {
    var langId = form.lid.value;

    var langFrom = ''; // autodetect
    var langTo = langCodes[langId];

    Translate.translate(form.cpoint_name.value,langFrom,langTo,"cb_cpoint_name");
    Translate.translate(form.disconnect_instructions.value,langFrom,langTo,"cb_disconnect_instructions");
    Translate.translate(form.disconnect_state.value,langFrom,langTo,"cb_disconnect_state");
    if(useReconnect) {
        Translate.translate(form.reconnect_instructions.value,langFrom,langTo,"cb_reconnect_instructions");
    }
    Translate.translate(form.reconnect_state.value,langFrom,langTo,"cb_reconnect_state");
    if(useInspect) {
        Translate.translate(form.inspection_instructions.value,langFrom,langTo,"cb_inspection_instructions");
        Translate.translate(form.inspection_state.value,langFrom,langTo,"cb_inspection_state");
    }

}
ENDJSCODEB;

            $xoTheme->addScript(null, array( 'type' => 'text/javascript' ), $translate_js);

            $translate_button=new XoopsFormButton('', 'lchange', _MD_GWLOTO_LANG_TRANS_BUTTON, 'button');
            $translate_button->setExtra(' onClick=\'doTranslate(this.form)\' ');
            $langtray->addElement($translate_button);
        }

// end microsoft translate support

        $form->addElement($langtray);
    }

    //$form->display();
    $body=$form->render();


//$debug="op=$op  language=$language displayed_lid=$displayed_lid <br />";
//$debug.='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

//$canedit=isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]);
//$media=getAttachedMedia('point', $currentpoint, $language, $canedit);

setPageTitle(_MD_GWLOTO_TITLE_EDITPOINT);

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
