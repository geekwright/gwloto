<?php
/**
* viewpoint.php - view control point detail
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
$currentscript=basename(__FILE__) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include('include/userauth.php');
include('include/userauthlist.php');
include('include/common.php');
include('include/placeenv.php');
include('include/actionmenu.php');

// leave if we don't have any control plan authority
if (!(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_CP_VIEW]) ||
isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS]))) {
    redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

$show_edit=false;
if (isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) ||
isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
    $show_edit=true;
}

$op='display';
if (isset($_POST['submit'])) {
    $op='update';
}

// translator functionality
if (isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
    if (isset($_POST['lchange']) || isset($_GET['lchange'])) {
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
        $err_message = _MD_GWLOTO_EDITPLAN_NOTFOUND;
        redirect_header('index.php', 3, $err_message);
    }

    $cpointcnt=getCPointCounts($currentplan);

    // $cplan_name=nl2br($cplan_name);
    $disconnect_instructions = nl2br($disconnect_instructions);
    $reconnect_instructions = nl2br($reconnect_instructions);
    $inspection_instructions = nl2br($inspection_instructions);

    $token=0;

    $cplan_name=getCplanName($currentplan, $language);
    $caption = _MD_GWLOTO_VIEWPOINT_FORM;
    $form = new XoopsThemeForm($caption, 'form1', 'viewpoint.php', 'GET', $token);

    $caption = _MD_GWLOTO_EDITPLAN_NAME;
    $form->addElement(new XoopsFormLabel($caption, '<a href="viewplan.php?cpid='.$currentplan.'">'.$cplan_name.'</a>', 'cplan_name'), false);

    $caption = _MD_GWLOTO_EDITPOINT_NAME;
    $form->addElement(new XoopsFormLabel($caption, $cpoint_name, 'cpoint_name'), false);

    $caption = _MD_GWLOTO_EDITPOINT_LOCKS_REQ;
    $form->addElement(new XoopsFormLabel($caption, $locks_required, 'locks_required'), false);

    $caption = _MD_GWLOTO_EDITPOINT_TAGS_REQ;
    $form->addElement(new XoopsFormLabel($caption, $tags_required, 'tags_required'), false);

    $caption = _MD_GWLOTO_EDITPOINT_DISC_STATE;
    $form->addElement(new XoopsFormLabel($caption, $disconnect_state, 'disconnect_state'), false);

    $caption = _MD_GWLOTO_EDITPOINT_DISC_INST;
    $form->addElement(new XoopsFormLabel($caption, $disconnect_instructions, 'disconnect_instructions'), false);

if ($xoopsModuleConfig['show_inspect']) {
    $caption = _MD_GWLOTO_EDITPOINT_INSP_STATE;
    $form->addElement(new XoopsFormLabel($caption, $inspection_state, 'inspection_state'), false);

    $caption = _MD_GWLOTO_EDITPOINT_INSP_INST;
    $form->addElement(new XoopsFormLabel($caption, $inspection_instructions, 'inspection_instructions'), false);
}

    $caption = _MD_GWLOTO_EDITPOINT_RECON_STATE;
    $form->addElement(new XoopsFormLabel($caption, $reconnect_state, 'reconnect_state'), false);

if ($xoopsModuleConfig['show_reconnect']) {
    $caption = _MD_GWLOTO_EDITPOINT_RECON_INST;
    $form->addElement(new XoopsFormLabel($caption, $reconnect_instructions, 'reconnect_instructions'), false);
}

    $member_handler =& xoops_gethandler('member');
    $thisUser =& $member_handler->getUser($last_changed_by);
        if (!is_object($thisUser) || !$thisUser->isActive()) {
            $user_name=$last_changed_by;
        } else {
            $user_name = $thisUser->getVar('name');
            if ($user_name=='') {
                $user_name = $thisUser->getVar('uname');
            }
        }
    $caption = _MD_GWLOTO_LASTCHG_BY;
    $form->addElement(new XoopsFormLabel($caption, $user_name, 'last_changed_by'), false);

    $caption = _MD_GWLOTO_LASTCHG_ON;
    $form->addElement(new XoopsFormLabel($caption, getDisplayDate($last_changed_on), 'last_changed_on'), false);

    $form->addElement(new XoopsFormHidden('ptid', $currentpoint));

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

    $form->addElement($langtray);
}

// $form->addElement(new XoopsFormButton(_MD_GWLOTO_EDITPLAN_UPDATE, 'submit', _MD_GWLOTO_EDITPLAN_UPDATE_BUTTON, 'submit'));

    //$form->display();
    $body=$form->render();

$cpoints=getControlPoints($currentplan, $language, 'seq_disconnect');
if (isset($cpoints)) {
    $xoopsTpl->assign('cpoints', $cpoints);
}

//$canedit=false;
//$media=getAttachedMedia('point', $currentpoint, $language, $canedit);

//$debug="op=$op  language=$language displayed_lid=$displayed_lid <br />";
//$debug.='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

setPageTitle(_MD_GWLOTO_TITLE_VIEWPOINT);

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
