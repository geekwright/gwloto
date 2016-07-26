<?php
/**
* newplace.php - add a new place definition
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
$currentscript=basename(__FILE__) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include('include/userauth.php');
include('include/userauthlist.php');
include('include/common.php');
include('include/placeenv.php');
include('include/actionmenu.php');

// leave if we don't have place administrator authority
if (!isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT])) {
    redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

$op='display';
if (isset($_POST['submit'])) {
    $op='add';
}

$place_name='';
$place_hazard_inventory='';
$place_required_ppe='';

    if (isset($_POST['place_name'])) {
        $place_name = cleaner($_POST['place_name']);
    }
    if (isset($_POST['place_hazard_inventory'])) {
        $place_hazard_inventory = cleaner($_POST['place_hazard_inventory']);
    }
    if (isset($_POST['place_required_ppe'])) {
        $place_required_ppe = cleaner($_POST['place_required_ppe']);
    }

if ($op!='display') {
    $check=$GLOBALS['xoopsSecurity']->check();

    if (!$check) {
        $op='display';
        $err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
    }
}

if ($op=='add') {
    $sl_place_name=dbescape($place_name);
    $sl_place_hazard_inventory=dbescape($place_hazard_inventory);
    $sl_place_required_ppe=dbescape($place_required_ppe);

    $dberr=false;
    $dbmsg='';
    startTransaction();
    // insert place
    $sql ="INSERT INTO ".$xoopsDB->prefix('gwloto_place');
    $sql.=" (parent_id) VALUES ($currentplace)";
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        $dberr=true;
        $dbmsg=formatDBError();
    }
    // insert place_detail
    if (!$dberr) {
        $new_place_id = $xoopsDB->getInsertId();
        $sql ="INSERT INTO ".$xoopsDB->prefix('gwloto_place_detail');
        $sql.=" (place, language_id, place_name, place_hazard_inventory, place_required_ppe, last_changed_by, last_changed_on)";
        $sql.=" VALUES ($new_place_id, 0, '$sl_place_name', '$sl_place_hazard_inventory', '$sl_place_required_ppe', $myuserid, UNIX_TIMESTAMP() )";

        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $dberr=true;
            $dbmsg=formatDBError();
        }
    }

    if (!$dberr) {
        commitTransaction();
        // $message = _MD_GWLOTO_NEWPLACE_ADD_OK;
        redirect_header("editplace.php?pid=$new_place_id", 3, _MD_GWLOTO_NEWPLACE_ADD_OK);
    } else {
        rollbackTransaction();
        $err_message = _MI_GWLOTO_AD_PLACE_ADD_ERR .' '.$dbmsg;
    }
}

    $token=true;

    $formtitle=sprintf(_MD_GWLOTO_NEWPLACE_FORM, getPlaceName($currentplace, $language));
    $form = new XoopsThemeForm($formtitle, 'form1', 'newplace.php', 'POST', $token);

    $caption = _MD_GWLOTO_EDITPLACE_NAME;
    $form->addElement(new XoopsFormText($caption, 'place_name', 40, 250, htmlspecialchars($place_name, ENT_QUOTES)), true);

    $caption = _MD_GWLOTO_EDITPLACE_HAZARDS;
    $form->addElement(new XoopsFormTextArea($caption, 'place_hazard_inventory', $place_hazard_inventory, 15, 50, 'place_hazard_inventory'), false);

    $caption = _MD_GWLOTO_EDITPLACE_PPE;
    $form->addElement(new XoopsFormTextArea($caption, 'place_required_ppe', $place_required_ppe, 10, 50, 'place_required_ppe'), false);

    $form->addElement(new XoopsFormHidden('pid', $currentplace));

    $form->addElement(new XoopsFormButton(_MD_GWLOTO_NEWPLACE_ADD_BUTTON_DSC, 'submit', _MD_GWLOTO_NEWPLACE_ADD_BUTTON, 'submit'));

    //$form->display();
    $body.=$form->render();

//$debug="op=$op  language=$language displayed_lid=$displayed_lid <br />";
//$debug.='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

if (isset($body)) {
    $xoopsTpl->assign('body', $body);
}

setPageTitle(_MD_GWLOTO_TITLE_NEWPLACE);

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
