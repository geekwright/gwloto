<?php
/**
* newjob.php - create a new job from a control plan
*
* This file is part of gwloto - geekwright lockout tagout
*
* @copyright  Copyright © 2010-2011 geekwright, LLC. All rights reserved.
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
include('include/jobstatus.php');

$local_js = <<<ENDJSCODE
function html_entity_decode(str) {
 try {
  var tarea=document.createElement('textarea');
  tarea.innerHTML = str;
  return tarea.value;
  var decodedStr = tarea.value;
  document.removeElement(tarea);
  return decodedStr;
 }
 catch(e) {
    return str;
 }
}
ENDJSCODE;

$xoTheme->addScript(null, array( 'type' => 'text/javascript' ), $local_js);

// leave if we don't have job edit authority
if (!isset($places['currentauth'][_GWLOTO_USERAUTH_JB_EDIT])) {
    redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

if (!isset($currentplan)) {
    redirect_header('index.php', 3, _MD_GWLOTO_EDITPLAN_NOTFOUND);
}

$op='display';
if (isset($_POST['submit'])) {
    $op='add';
}

$job_name='';
$job_workorder='';
$job_supervisor='';
$job_startdate='';
$job_enddate='';
$job_description='';
$job_status='planning';
$step_name='';
$assigned_uid=$myuserid;
$job_step_status='planning';

if (isset($_POST['job_name'])) {
    $job_name = cleaner($_POST['job_name']);
}
if (isset($_POST['job_workorder'])) {
    $job_workorder = cleaner($_POST['job_workorder']);
}
if (isset($_POST['job_supervisor'])) {
    $job_supervisor = cleaner($_POST['job_supervisor']);
}
if (isset($_POST['job_startdate'])) {
    $job_startdate = cleaner($_POST['job_startdate']);
}
if (isset($_POST['job_enddate'])) {
    $job_enddate = cleaner($_POST['job_enddate']);
}
if (isset($_POST['job_description'])) {
    $job_description = cleaner($_POST['job_description']);
}
if (isset($_POST['step_name'])) {
    $step_name = cleaner($_POST['step_name']);
}
if (isset($_POST['assigned_uid'])) {
    $assigned_uid = intval($_POST['assigned_uid']);
}

if ($op!='display') {
    $check=$GLOBALS['xoopsSecurity']->check();

    if (!$check) {
        $op='display';
        $err_message = _MD_GWLOTO_MSG_BAD_TOKEN;
    }
}

if ($op=='add') {
    $sl_job_name=dbescape($job_name);
    $sl_job_workorder=dbescape($job_workorder);
    $sl_job_supervisor=dbescape($job_supervisor);
    $sl_job_startdate=dbescape($job_startdate);
    $sl_job_enddate=dbescape($job_enddate);
    $sl_job_description=dbescape($job_description);
    $sl_step_name=dbescape($step_name);

    $dberr=false;
    $dbmsg='';
    startTransaction();

    $sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_job');
    $sql.='   (job_name, job_workorder, job_supervisor, job_startdate, job_enddate, job_description, job_status, last_changed_by, last_changed_on) ';
    $sql.=" VALUES ('$sl_job_name', '$sl_job_workorder', '$sl_job_supervisor', '$sl_job_startdate', '$sl_job_enddate', '$sl_job_description', 'planning', $myuserid, UNIX_TIMESTAMP() )";
    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        $dberr=true;
        $dbmsg=formatDBError();
    }

    if (!$dberr) {
        $new_job_id = $xoopsDB->getInsertId();
        $sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_job_steps');
        $sql.=' (job, cplan, step_name, assigned_uid, job_step_status, last_changed_by, last_changed_on) ';
        $sql.=" VALUES ($new_job_id, $currentplan, '$sl_step_name', $assigned_uid, 'planning', $myuserid, UNIX_TIMESTAMP() )";

        $result = $xoopsDB->queryF($sql);
        if (!$result) {
            $dberr=true;
            $dbmsg=formatDBError();
        }
    }

    if (!$dberr) {
        foreach ($places['chaindown'] as $v) {
            $sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_job_places');
            $sql.=' (job, cplan, place) ';
            $sql.=" VALUES ($new_job_id, $currentplan, $v)";

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
        $message = _MD_GWLOTO_JOB_ADD_OK;
        redirect_header("viewjob.php?jid=$new_job_id", 3, $message);
    } else {
        rollbackTransaction();
        $err_message = _MD_GWLOTO_JOB_ADD_DB_ERROR .' '.$dbmsg;
    }
}

    $jobreqs=getJobReqs();
    $token=true;

    $formtitle=_MD_GWLOTO_JOB_NEW_FORM;

    $form = new XoopsThemeForm($formtitle, 'form1', 'newjob.php', 'POST', $token);

    $caption = _MD_GWLOTO_JOB_NAME;
    $form->addElement(new XoopsFormText($caption, 'job_name', 40, 250, htmlspecialchars($job_name, ENT_QUOTES)), true);

    $caption = _MD_GWLOTO_JOB_WORKORDER;
    $form->addElement(new XoopsFormText($caption, 'job_workorder', 40, 250, htmlspecialchars($job_workorder, ENT_QUOTES)), $jobreqs['workorder']);

    $caption = _MD_GWLOTO_JOB_SUPERVISOR;
//  $form->addElement(new XoopsFormText($caption, 'job_supervisor', 40, 80, htmlspecialchars($job_supervisor, ENT_QUOTES)),false);
    $svtray=new XoopsFormElementTray($caption, '<br />');

    $svtray->addElement(new XoopsFormText('', 'job_supervisor', 40, 80, htmlspecialchars($job_supervisor, ENT_QUOTES)), $jobreqs['supervisor']);

    $uid_choices=getUsersByAuth(_GWLOTO_USERAUTH_PL_SUPER, $places['chaindown'], false);
    $listboxsv = new XoopsFormSelect('', 'pick_supervisor', $myuserid, 1, false);
    $listboxsv->addOption('', _MD_GWLOTO_JOB_PICKSUPER);
    foreach ($uid_choices as $i) {
        $listboxsv->addOption($i, $i);
    }
    $listboxsv->setExtra('onChange="this.form.elements[\'job_supervisor\'].value = html_entity_decode(this.form.elements[\'pick_supervisor\'].value) " ');
    $svtray->addElement($listboxsv);
    $form->addElement($svtray);

    $caption = _MD_GWLOTO_JOB_STARTDATE;
    $form->addElement(new XoopsFormText($caption, 'job_startdate', 12, 30, htmlspecialchars($job_startdate, ENT_QUOTES)), $jobreqs['startdate']);

    $caption = _MD_GWLOTO_JOB_ENDDATE;
    $form->addElement(new XoopsFormText($caption, 'job_enddate', 12, 30, htmlspecialchars($job_enddate, ENT_QUOTES)), $jobreqs['enddate']);

    $caption = _MD_GWLOTO_JOB_DESCRIPTION;
    $form->addElement(new XoopsFormTextArea($caption, 'job_description', $job_description, 10, 50, 'job_description'), $jobreqs['description']);

    $caption = _MD_GWLOTO_JOBSTEP_PLAN;
    $cplan_name=getCplanName($currentplan, $language);
    $form->addElement(new XoopsFormLabel($caption, $cplan_name, 'cplan_name'), false);

    $caption = _MD_GWLOTO_JOBSTEP_NAME;
    $form->addElement(new XoopsFormText($caption, 'step_name', 40, 250, htmlspecialchars($step_name, ENT_QUOTES)), $jobreqs['stepname']);

    $caption = _MD_GWLOTO_JOBSTEP_ASSIGNED_UID;
    $uid_choices=getUsersByAuth(_GWLOTO_USERAUTH_JB_EDIT, $places['chaindown'], false);
    $listboxau = new XoopsFormSelect($caption, 'assigned_uid', $assigned_uid, 1, false);
    foreach ($uid_choices as $i=>$v) {
        $listboxau->addOption($i, $v);
    }
    $form->addElement($listboxau, true);


    $form->addElement(new XoopsFormHidden('cpid', $currentplan));

    $form->addElement(new XoopsFormButton(_MD_GWLOTO_JOB_ADD_BUTTON_DSC, 'submit', _MD_GWLOTO_JOB_ADD_BUTTON, 'submit'));

    //$form->display();
    $body=$form->render();

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//$debug.='<pre>$jobs='.print_r($jobs,true).'</pre>';

if (isset($body)) {
    $xoopsTpl->assign('body', $body);
}

setPageTitle(_MD_GWLOTO_TITLE_NEWJOB);

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
