<?php
/**
* editauths.php - edit authorities for a user at a place
*
* This file is part of gwloto - geekwright lockout tagout
*
* @copyright  Copyright © 2010-2010 geekwright, LLC. All rights reserved.
* @license    gwloto/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwloto
* @version    $Id$
*/

include '../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_editauths.html';
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

// leave if we don't have place administrator authority
if (!isset($places['currentauth'][_GWLOTO_USERAUTH_PL_ADMIN]) &&
   !isset($places['currentauth'][_GWLOTO_USERAUTH_PL_AUDIT])) {
    redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

if (isset($_GET['uid'])) {
    $auth_uid = intval($_GET['uid']);
}
if (isset($_POST['auth_uid'])) {
    $auth_uid = intval($_POST['auth_uid']);
}
if (!isset($auth_uid)) {
    $auth_uid=$myuserid;
}

$op='display';
if (isset($_POST['submit'])) {
    $op='update';
    // quietly disallow if we are in audit mode
    if (!isset($places['currentauth'][_GWLOTO_USERAUTH_PL_ADMIN])) {
        $op='display';
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
    $dberr=false;
    $dbmsg='';
    startTransaction();
    $sql ="DELETE FROM  ".$xoopsDB->prefix('gwloto_user_auth');
    $sql.=" WHERE place_id = $currentplace AND uid=$auth_uid";
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        $dberr=true;
        $dbmsg=formatDBError();
    }

    if (!$dberr) {
        $uath=array();
        if (isset($_POST['uauth'])) {
            $uauth = $_POST['uauth'];
        }
        foreach ($uauth as $authority) {
            $sql ="INSERT INTO ".$xoopsDB->prefix('gwloto_user_auth');
            $sql.=" (uid, place_id, authority, last_changed_by, last_changed_on)";
            $sql.=" VALUES ($auth_uid, $currentplace, $authority, $myuserid, UNIX_TIMESTAMP() )";
            $result = $xoopsDB->queryF($sql);
            if (!$result) {
                $dberr=true;
                $dbmsg=formatDBError();
                break;
            }
        }
    }

    if (!$dberr) {
        commitTransaction();
        $message = _MD_GWLOTO_USERAUTH_UPDATE_OK;
    } else {
        rollbackTransaction();
        $err_message .= _MD_GWLOTO_USERAUTH_DB_ERROR .' '.$dbmsg;
    }
}

$usersauths=array();

    $sql='SELECT authority FROM '.$xoopsDB->prefix('gwloto_user_auth');
    $sql.=" WHERE uid=$auth_uid AND place_id=$currentplace";

    $result = $xoopsDB->query($sql);
    $cnt=0;
    if ($result) {
        while ($myrow=$xoopsDB->fetchArray($result)) {
            $usersauths[$myrow['authority']]=true;
            ++$cnt;
        }
    }

    $token=true;

    $form = new XoopsThemeForm(_MD_GWLOTO_USERAUTH_FORM, 'form1', 'editauths.php', 'POST', $token);

    // caption, name, include_annon, size (1 for dropdown), multiple
    $form->addElement(new XoopsFormSelectUser(_MD_GWLOTO_USERAUTH_USER, 'auth_uid', true, $auth_uid, 1, false), true);

    $form->addElement(new XoopsFormButton(_MD_GWLOTO_USERAUTH_DISPLAY, 'lookup', _MD_GWLOTO_USERAUTH_DISPLAY_BUTTON, 'submit'));

    $form->addElement(new XoopsFormHidden('pid', $currentplace));

    if (isset($places['currentauth'][_GWLOTO_USERAUTH_PL_ADMIN])) {
        $caption = _MD_GWLOTO_USERAUTH_AUTHS;
        $checked_values=array();
        foreach ($UserAuthList as $i => $v) {
            if (isset($usersauths[$i])) {
                $checked_values[$i]=$i;
            }
        }
        $checkbox = new XoopsFormCheckBox($caption, 'uauth', $checked_values, '<br />');
        foreach ($UserAuthList as $i => $v) {
            $checkbox->addOption($i, $v);
        }
        $form->addElement($checkbox);
        unset($checkbox); //destroy first instance

        $form->addElement(new XoopsFormButton(_MD_GWLOTO_USERAUTH_UPDATE, 'submit', _MD_GWLOTO_USERAUTH_UPDATE_BUTTON, 'submit'));
    }

    //$form->display();
    $body=$form->render();

//

$authbyplace=array();
$cnt=0;
foreach ($places['name'] as $pid => $pname) {
    $sql='SELECT authority, \'\' as authsource, uid as authid FROM  ' . $xoopsDB->prefix('gwloto_user_auth');
    $sql.=" WHERE uid=$auth_uid AND place_id = $pid ";

    $sql.=' UNION ';

    $sql.=' SELECT authority, g.name as authsource, a.groupid as authid FROM ';
    $sql.=$xoopsDB->prefix('gwloto_group_auth').' a ';
    $sql.=', '.$xoopsDB->prefix('groups_users_link').' l ';
    $sql.=', '.$xoopsDB->prefix('groups').' g ';
    $sql.=" WHERE uid=$auth_uid AND place_id = $pid ";
    $sql.=' AND a.groupid = l.groupid and g.groupid = l.groupid ';

    $sql.=' ORDER BY authority, authsource, authid ';


    $result = $xoopsDB->query($sql);

    if ($result) {
        while ($myrow=$xoopsDB->fetchArray($result)) {
            $authbyplace[$cnt]['pid']=$pid;
            $authbyplace[$cnt]['authority']=$myrow['authority'];
            $authbyplace[$cnt]['pname']=$pname;
            $authbyplace[$cnt]['aname']=$UserAuthList[$myrow['authority']];
            $authbyplace[$cnt]['authsource']=$myrow['authsource'];
            $authbyplace[$cnt]['authid']=$myrow['authid'];

            if ($myrow['authsource']=='') {
                $authbyplace[$cnt]['authurl']='editauths.php?uid='.$myrow['authid'].'&pid='.$pid;
            } else {
                $authbyplace[$cnt]['authurl']='editgrpauths.php?gid='.$myrow['authid'].'&pid='.$pid;
            }

            ++$cnt;
        }
    }
}

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

if (isset($body)) {
    $xoopsTpl->assign('body', $body);
}

$xoopsTpl->assign('auth_uid', $auth_uid);
$xoopsTpl->assign('crumburl', '');
$xoopsTpl->assign('crumbextra', '&uid='.$auth_uid);
$xoopsTpl->assign('crumbpcurl', '');
$xoopsTpl->assign('crumbpcextra', "<input type='hidden' name='uid' value='$auth_uid'>");

if (isset($authbyplace)) {
    $xoopsTpl->assign('report', $authbyplace);
}

setPageTitle(_MD_GWLOTO_TITLE_EDITAUTHS);


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
