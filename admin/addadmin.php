<?php
/**
* addplace.php - add a top level place and associated administrator authority
*
* This file is part of gwloto - geekwright lockout tagout
*
* @copyright  Copyright © 2010 geekwright, LLC. All rights reserved. 
* @license    gwloto/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwloto
* @version    $Id: addplace.php 4 2010-09-11 02:19:21Z rgriffith $
*/

include 'header.php';
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
include ('../include/userauth.php');

if($xoop25plus) {
	echo $moduleAdmin->addNavigation('addplace.php');
}
else { // !$xoop25plus
	adminmenu(3);
}

	$myuserid = $xoopsUser->getVar('uid');

	$admin_uid=$myuserid;
	$pid=0;
	if(isset($_POST['pid'])) $pid = intval($_POST['pid']);
	if(isset($_POST['admin_uid'])) $admin_uid = intval($_POST['admin_uid']);
	$authority=_GWLOTO_USERAUTH_PL_ADMIN;

	if($pid==0)  {
			$message = _MI_GWLOTO_AD_PLACE_ADD_ADMIN_ERR;
			redirect_header('addplace.php', 3, $message);
	}

	$place_name='';
	$sql='SELECT place_name FROM '.$xoopsDB->prefix('gwloto_place_detail');
	$sql.=" WHERE place = $pid and language_id=0 ";

	$cnt=0;
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$place_name=$myrow['place_name'];
		}
	}

	$op='display';
	if(isset($_POST['op'])) $op = cleaner($_POST['op']);

	if($op != 'add') $op='display';

	if($op=='add') {
		$check=$GLOBALS['xoopsSecurity']->check();
		if (!$check) {
			$op='display';
			$err_message = _AD_GWLOTO_BAD_TOKEN;
		}
	}

	if($op=='add') {
		$dberr=false;
		$dbmsg='';
		if(!$dberr) {
			// insert new gwloto_user_auth
			$sql ="INSERT INTO ".$xoopsDB->prefix('gwloto_user_auth');
			$sql.=" (uid, place_id, authority, last_changed_by, last_changed_on)";
			$sql.=" VALUES ($admin_uid, $pid, $authority, $myuserid, UNIX_TIMESTAMP() )";
			$result = $xoopsDB->queryF($sql);
			if (!$result) {
				$dberr=true;
				$dbmsg=formatDBError();
			}
		}

		if (!$dberr) {
			$message = _MI_GWLOTO_AD_PLACE_ADD_ADMIN_OK;
			redirect_header('addplace.php', 3, $message);
		}
		else {
			$err_message = _MI_GWLOTO_AD_PLACE_ADD_ADMIN_ERR .' '.$dbmsg;
		}
	}

	if(isset($err_message)) echo "<br><br><b>$err_message</b><br><br>";

	$token=1;

	$form = new XoopsThemeForm(_MI_GWLOTO_AD_PLACE_ADD_ADMIN, 'form1', 'addadmin.php', 'POST', $token);

	$caption = _MI_GWLOTO_AD_PLACE_NAME;
	$form->addElement(new XoopsFormLabel($caption, $place_name, 'place_name'),false);
	// caption, name, include_annon, size (1 for dropdown), multiple
	$caption = _MI_GWLOTO_AD_PLACE_ADMIN;
	$form->addElement(new XoopsFormSelectUser($caption, 'admin_uid', false, $admin_uid, 1, false),true);

	$form->addElement(new XoopsFormHidden('op', 'add'));
	$form->addElement(new XoopsFormHidden('pid', $pid));

	$form->addElement(new XoopsFormButton(_MI_GWLOTO_AD_PLACE_ADD_ADMIN, 'submit', _MI_GWLOTO_AD_PLACE_ADD_ADMIN, 'submit'));

	//$form->display();
	$body=$form->render();

echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td width='100%' >";
echo $body;
echo '</td></tr>';
echo "</table>";

echo "<table width='100%' border='0' cellspacing='1' class='outer'>";
echo '<tr><th colspan="3" align="center">'._MI_GWLOTO_AD_PLACE_LISTNAME.'</th></tr>';
echo '<tr><th>'._MI_GWLOTO_AD_PLACE_ID."</th><th>"._MI_GWLOTO_AD_PLACE_NAME.'</th>';
echo '<th>'._MI_GWLOTO_AD_TODO_ACTION.'</th></tr>';

$sql="SELECT place_id, place_name FROM ".$xoopsDB->prefix('gwloto_place').' ,' . $xoopsDB->prefix('gwloto_place_detail');
$sql.=" WHERE place = place_id and parent_id=0 and language_id=0";
$sql.=" ORDER BY place_name ";

$result = $xoopsDB->query($sql);
$cnt=0;
$teven='class="even"';
$todd='class="odd"';
$tclass=$teven;
if ($result) {
	while($myrow=$xoopsDB->fetchArray($result)) {
		++$cnt;
		echo '<tr cellspacing="2" cellpadding="2" '.$tclass.'>';
		echo '<td>'.$myrow['place_id'].'</td><td>'.$myrow['place_name'].'</td>';
		$form='<form name="addadmin" action="addadmin.php" method="post">';
		$form.='<input type="hidden" name="pid" value="'.$myrow['place_id'].'" />';
		$form.='<input type="submit" value="'._MI_GWLOTO_AD_PLACE_ADD_ADMIN.'" />';
		$form.='</form>';
		echo '<td>'.$form.'</td></tr>';
	}
}
if($cnt==0) echo '<tr><td colspan="3" align="center">'._MI_GWLOTO_AD_PLACE_LISTEMPTY.'</td></tr>';
echo "</table>";

include 'footer.php';
?>
