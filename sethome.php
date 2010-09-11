<?php
/**
* sethome.php - set a user's home place
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
include ('include/userauth.php');
include ('include/userauthlist.php');
include ('include/common.php');
include ('include/placeenv.php');
include ('include/actionmenu.php');

if(isset($currentplace)) {
	if($myuserid!=0) {
		$sql ='UPDATE '.$xoopsDB->prefix('gwloto_user');
		$sql.=" SET default_place_id = $currentplace ";
		$sql.=" , last_changed_by = $myuserid ";
		$sql.=" , last_changed_on = UNIX_TIMESTAMP() ";
		$sql.=" WHERE uid = $myuserid";
		$result = $xoopsDB->queryF($sql);
		$rcnt=$xoopsDB->getAffectedRows();
		if($rcnt==0) {
			$sql ='INSERT into '.$xoopsDB->prefix('gwloto_user');
			$sql.="(uid, language_id, default_place_id, last_changed_by, last_changed_on) ";
			$sql.="VALUES ($myuserid, $language, $currentplace, $myuserid, UNIX_TIMESTAMP())";
			$result = $xoopsDB->queryF($sql);
		}

		redirect_header('index.php', 3, _MD_GWLOTO_SETHOME_OK);
	}
}

redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);

include(XOOPS_ROOT_PATH.'/footer.php');
?>
