<?php
/**
* editgrpauths.php - edit authorities for a group at a place
*
* This file is part of gwloto - geekwright lockout tagout
*
* @copyright  Copyright © 2011 geekwright, LLC. All rights reserved. 
* @license    gwloto/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwloto
* @version    $Id$
*/

include '../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_editauths.html';
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
if(!isset($places['currentauth'][_GWLOTO_USERAUTH_PL_ADMIN]) &&
   !isset($places['currentauth'][_GWLOTO_USERAUTH_PL_AUDIT])) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

if(isset($_GET['gid'])) $auth_groupid = intval($_GET['gid']);
if(isset($_POST['auth_groupid'])) $auth_groupid = intval($_POST['auth_groupid']);

$op='display';
if(isset($_POST['submit'])) {
	$op='update';
	// quietly disallow if we are in audit mode
	if(!isset($places['currentauth'][_GWLOTO_USERAUTH_PL_ADMIN])) {
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

if($op=='update') {
	$dberr=false;
	$dbmsg='';
	startTransaction();
	$sql ="DELETE FROM  ".$xoopsDB->prefix('gwloto_group_auth');
	$sql.=" WHERE place_id = $currentplace AND groupid=$auth_groupid";
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$gath=array();
		if(isset($_POST['gauth'])) $gauth = $_POST['gauth'];
		foreach ($gauth as $authority) {
			$sql ="INSERT INTO ".$xoopsDB->prefix('gwloto_group_auth');
			$sql.=" (groupid, place_id, authority, last_changed_by, last_changed_on)";
			$sql.=" VALUES ($auth_groupid, $currentplace, $authority, $myuserid, UNIX_TIMESTAMP() )";
			$result = $xoopsDB->queryF($sql);
			if (!$result) {
				$dberr=true;
				$dbmsg=formatDBError();
				break;
			}
		}
	}

	if(!$dberr) {
		commitTransaction();
		$message = _MD_GWLOTO_USERAUTH_UPDATE_OK;
	}
	else {
		rollbackTransaction();
		$err_message .= _MD_GWLOTO_USERAUTH_DB_ERROR .' '.$dbmsg;
	}
}

$usersauths=array();

	$sql='SELECT authority FROM '.$xoopsDB->prefix('gwloto_group_auth');
	$sql.=" WHERE groupid=$auth_groupid AND place_id=$currentplace";

	$result = $xoopsDB->query($sql);
	$cnt=0;
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$usersauths[$myrow['authority']]=true;
			++$cnt;
		}
	}

	$token=true;

	$form = new XoopsThemeForm(_MD_GWLOTO_GROUPAUTH_FORM, 'form1', 'editgrpauths.php', 'POST', $token);

	// caption, name, include_annon, value, size (1 for dropdown), multiple
	$form->addElement(new XoopsFormSelectGroup(_MD_GWLOTO_GROUPAUTH_GROUP, 'auth_groupid', false, $auth_groupid, 1, false),true);

	$form->addElement(new XoopsFormButton(_MD_GWLOTO_GROUPAUTH_DISPLAY, 'lookup', _MD_GWLOTO_USERAUTH_DISPLAY_BUTTON, 'submit'));

	$form->addElement(new XoopsFormHidden('pid', $currentplace));

	if(isset($places['currentauth'][_GWLOTO_USERAUTH_PL_ADMIN])) {
		$caption = _MD_GWLOTO_USERAUTH_AUTHS;
		$checked_values=array();
		foreach ($UserAuthList as $i => $v) {
			if(isset($usersauths[$i])) $checked_values[$i]=$i;
		}
		$checkbox = new XoopsFormCheckBox($caption, 'gauth', $checked_values,'<br />');
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
foreach($places['name'] as $pid => $pname) {

	$sql=' SELECT authority, g.name as authsource, a.groupid as authid FROM ';
	$sql.=$xoopsDB->prefix('gwloto_group_auth').' a ';
	$sql.=', '.$xoopsDB->prefix('groups').' g ';
	$sql.=" WHERE a.groupid=$auth_groupid AND place_id = $pid ";
	$sql.=' AND a.groupid = g.groupid ';

	$sql.=' ORDER BY authority, authsource, authid ';

	$result = $xoopsDB->query($sql);

	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$authbyplace[$cnt]['pid']=$pid;
			$authbyplace[$cnt]['authority']=$myrow['authority'];
			$authbyplace[$cnt]['pname']=$pname;
			$authbyplace[$cnt]['aname']=$UserAuthList[$myrow['authority']];
			$authbyplace[$cnt]['authsource']=$myrow['authsource'];
			$authbyplace[$cnt]['authid']=$myrow['authid'];

			if($myrow['authsource']=='') 
				$authbyplace[$cnt]['authurl']='editauths.php?uid='.$myrow['authid'].'&pid='.$pid;
			else
				$authbyplace[$cnt]['authurl']='editgrpauths.php?gid='.$myrow['authid'].'&pid='.$pid;

			++$cnt;
		}
	}
}

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';

if(isset($body)) $xoopsTpl->assign('body', $body);

$xoopsTpl->assign('auth_uid', $auth_groupid);
$xoopsTpl->assign('crumburl','');
$xoopsTpl->assign('crumbextra','&gid='.$auth_groupid);
$xoopsTpl->assign('crumbpcurl','');
$xoopsTpl->assign('crumbpcextra',"<input type='hidden' name='gid' value='$auth_groupid'>");

if(isset($authbyplace)) $xoopsTpl->assign('report', $authbyplace);

setPageTitle(_MD_GWLOTO_TITLE_EDITGRPAUTHS);


if(isset($places['choose'])) $xoopsTpl->assign('choose',$places['choose']);
if(isset($places['crumbs'])) $xoopsTpl->assign('crumbs',$places['crumbs']);

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
