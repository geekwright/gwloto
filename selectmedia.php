<?php
/**
* selectmedia.php - capture reference to selected entity on the clipboard
* to facilitate attaching media item(s) to it
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
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
include ('include/userauth.php');
include ('include/userauthlist.php');
include ('include/common.php');
if(isset($_GET['type']) && isset($_GET['id'])) {
	$attach_type=cleaner($_GET['type']);
	$generic_id=intval($_GET['id']);

	switch($attach_type) {
		case 'place':
			$_GET['pid']=$generic_id;
			break;
		case 'plan':
			$_GET['cpid']=$generic_id;
			break;
		case 'point':
			$_GET['ptid']=$generic_id;
			break;
		case 'job':
			$_GET['jid']=$generic_id;
			break;
		case 'jobstep':
			$_GET['jsid']=$generic_id;
			break;
		default:
			redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
			break;
	}
}
else {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);
}
include ('include/placeenv.php');

// leave if we don't have any edit authority
if(!(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_JB_EDIT]) || isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT]) )) {
	redirect_header('index.php', 3, _MD_GWLOTO_MSG_NO_AUTHORITY);
}

setClipboard($myuserid,'mediaattach_'.$attach_type,$generic_id);
$message=_MD_GWLOTO_MEDIA_SELECT_TO_ATTACH;
redirect_header("listmedia.php?pid=$currentplace", 3, $message);

?>
