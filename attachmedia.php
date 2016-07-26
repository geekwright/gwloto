<?php
/**
* attachmedia.php - attach a media item to selected entity
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
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_editmedia.html';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename(__FILE__) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include('include/common.php');
include('include/placeenv.php');
include('include/actionmenu.php');

// leave if we don't have any media authority
if (!isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT]) &&
    !isset($places['currentauth'][_GWLOTO_USERAUTH_MD_VIEW]) &&
    !isset($places['currentauth'][_GWLOTO_USERAUTH_MD_TRANS])) {
    redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

// check for select mode on clipboard
    $caption='';
    $returnurl='';
    $attach_type='';
    $attachextra='';
    $parmtest=false; // will be set to true if clipboard matches passed environment, and user can edit

    $clipboard_type=$places['userinfo']['clipboard_type'];
    $generic_id=$places['userinfo']['clipboard_id'];

    switch ($clipboard_type) {
        case 'mediaattach_place':
            $caption=sprintf(_MD_GWLOTO_MEDIA_ATTACH_TO, _MD_GWLOTO_MEDIA_SELECT_TYPE_PLACE, getPlaceName($generic_id, $language));
            $returnurl="editplace.php?pid=$generic_id";
            $attach_type='place';
            $attachextra="pid";
            if (isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT])) {
                $parmtest=($generic_id==$currentplace);
            }
            break;
        case 'mediaattach_plan':
            $caption=sprintf(_MD_GWLOTO_MEDIA_ATTACH_TO, _MD_GWLOTO_MEDIA_SELECT_TYPE_PLAN, getCplanName($generic_id, $language));
            $returnurl="editplan.php?cpid=$generic_id";
            $attach_type='plan';
            $attachextra="cpid";
            if (isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
                $parmtest=($generic_id==$currentplan);
            }
            break;
        case 'mediaattach_point':
            $caption=sprintf(_MD_GWLOTO_MEDIA_ATTACH_TO, _MD_GWLOTO_MEDIA_SELECT_TYPE_POINT, getCpointName($generic_id, $language));
            $returnurl="editpoint.php?ptid=$generic_id";
            $attach_type='point';
            $attachextra="ptid";
            if (isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
                $parmtest=($generic_id==$currentpoint);
            }
            break;
        case 'mediaattach_job':
            $caption=sprintf(_MD_GWLOTO_MEDIA_ATTACH_TO, _MD_GWLOTO_MEDIA_SELECT_TYPE_JOB, getJobName($generic_id, $language));
            $returnurl="viewjob.php?jid=$generic_id";
            $attach_type='job';
            $attachextra="jid";
            if (checkJobAuthority($generic_id, $myuserid, true)) {
                $parmtest=($generic_id==$currentjob);
            }
            break;
        case 'mediaattach_jobstep':
            $caption=sprintf(_MD_GWLOTO_MEDIA_ATTACH_TO, _MD_GWLOTO_MEDIA_SELECT_TYPE_JOBSTEP, getJobStepName($generic_id, $language));
            $returnurl="viewstep.php?jsid=$generic_id";
            $attach_type='jobstep';
            $attachextra="jsid";
            if (checkJobAuthority($currentjob, $myuserid, true)) {
                $parmtest=($generic_id==$currentjobstep);
            }
            break;
    }

    $attach_to_caption=$caption;
    // if($caption!='') $xoopsTpl->assign('media_mode_select',$caption);

    // cancel button, or invalid parameters
    if (isset($_POST['media_cancel_button']) || $parmtest==false) {
        setClipboard($myuserid);
        if ($returnurl=='') {
            $returnurl='index.php';
        }
        redirect_header($returnurl, 3, _MD_GWLOTO_MEDIA_SELECT_CANCELED);
    }

    $op='display';

    if (isset($_POST['media_attach_button']) && $parmtest==true) {
        $op='add';
    }

    if ($op!='display') {
        $check=$GLOBALS['xoopsSecurity']->check();

        if (!$check) {
            $op='display';
            $err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
        }
    }

    if ($op=='add') {
        $required=0;
        $continue=false;
        if (isset($_POST['options'])) {
            foreach ($_POST['options'] as $v) {
                if ($v=='required') {
                    $required=1;
                }
                if ($v=='continue') {
                    $continue=true;
                }
            }
        }

        $sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_media_attach');
        $sql.=' (attach_type, generic_id, media_id, media_order, required, last_changed_by, last_changed_on) ';
        $sql.=" VALUES ('$attach_type', $generic_id, $currentmedia, 0, $required, $myuserid, UNIX_TIMESTAMP() )";
        $result = $xoopsDB->queryF($sql);
        if ($result) {
            if ($continue) {
                redirect_header('listmedia.php', 3, _MD_GWLOTO_MEDIA_ATTACH_OK);
            } else {
                setClipboard($myuserid);
                if ($returnurl=='') {
                    $returnurl='index.php';
                }
                redirect_header($returnurl, 3, _MD_GWLOTO_MEDIA_ATTACH_OK);
            }
        } else {
            $dberr=true;
            $dbmsg=formatDBError();
            $err_message=_MD_GWLOTO_MEDIA_ATTACH_DB_ERROR.' '.$dbmsg;
        }
    }

    $token=true;

    $formtitle=_MD_GWLOTO_MEDIA_ATTACH_FORM;

    $form = new XoopsThemeForm($formtitle, 'form1', 'attachmedia.php', 'POST', $token);

    $caption = _MD_GWLOTO_MEDIA_NAME;
    $form->addElement(new XoopsFormLabel($caption, '<a href="'."dlmedia.php?mid=$currentmedia&lid=$language".'" target="_blank">'.htmlspecialchars(getMediaName($currentmedia, $language), ENT_QUOTES).'</a>', 'media_name'));

    $caption = _MD_GWLOTO_MEDIA_ATTACH_TO_PROMPT;
    $form->addElement(new XoopsFormLabel($caption, '<a href="'.$returnurl.'">'.htmlspecialchars($attach_to_caption, ENT_QUOTES).'</a>', 'attach_to'));

    $form->addElement(new XoopsFormHidden('mid', $currentmedia));
    $form->addElement(new XoopsFormHidden($attachextra, $generic_id));

    $cb_req = new XoopsFormCheckBox(_MD_GWLOTO_MEDIA_ATTACH_OPTIONS, 'options', null, '<br />');
    $cb_req->addOption('required', _MD_GWLOTO_MEDIA_ATTACH_REQUIRED);
    $cb_req->addOption('continue', _MD_GWLOTO_MEDIA_ATTACH_CONTINUE);
    $form->addElement($cb_req);

    $caption = _MD_GWLOTO_MEDIA_TOOL_TRAY_DSC;
    $mttray=new XoopsFormElementTray($caption, '');

    $mttray->addElement(new XoopsFormButton('', 'media_attach_button', _MD_GWLOTO_MEDIA_ATTACH_BUTTON, 'submit'));
    $mttray->addElement(new XoopsFormButton('', 'media_cancel_button', _MD_GWLOTO_MEDIA_CANCEL_BUTTON, 'submit'));
    $form->addElement($mttray);

    $body=$form->render();

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//$debug.='<pre>$media='.print_r($media,true).'</pre>';

setPageTitle(_MD_GWLOTO_TITLE_ATTACHMEDIA);

$xoopsTpl->assign('crumburl', '');
//$xoopsTpl->assign('crumbextra','&uid='.$auth_uid);
$xoopsTpl->assign('crumbpcurl', '');
$xoopsTpl->assign('mediaclass', $mediaclass);  // for select list

if (isset($actions)) {
    $xoopsTpl->assign('actions', $actions);
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
