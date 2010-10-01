<?php
/**
* actionmenu.php - build menu based on context and permissions
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

if (!defined('XOOPS_ROOT_PATH')) die('Root path not defined');

$actions=array();
$show_select_action=true;
if(!isset($currentscript)) $currentscript='';

function addAction($link,$description) {
global $actions, $currentscript;
$addit=false;
	if ($currentscript=='') $addit=true;
	else {
		if (strncasecmp($currentscript,$link,strlen($currentscript))!=0) $addit=true;
	}

	if($addit) $actions[]=array('link'=>$link, 'description'=> $description);

	return $addit;
}

// Things to do with a media item
//if(isset($currentmedia) && $currentmedia!=0) {
//	if($show_select_action) {
//		if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT])) {
//			addAction("select.php?mid=$currentmedia",  _MD_GWLOTO_PRG_DSC_SELMEDIA);
//			$show_select_action=false;
//		}
//	}
//}

// Things to do with a control point
if(isset($currentpoint) && $currentpoint!=0) {
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || 
	   isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
		addAction("editpoint.php?ptid=$currentpoint",  _MD_GWLOTO_PRG_DSC_EDITPOINT);
	}
	if($show_select_action) {
		if(isset($places['alluserauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
			addAction("select.php?ptid=$currentpoint",  _MD_GWLOTO_PRG_DSC_SELPOINT);
			$show_select_action=false;
		}
	}
}

// Things to do with a control plan
if(isset($currentplan) && $currentplan!=0) {
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_JB_EDIT])) {
		addAction("newjob.php?cpid=$currentplan", _MD_GWLOTO_PRG_DSC_NEWJOB);
	}
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
		addAction("newpoint.php?cpid=$currentplan", _MD_GWLOTO_PRG_DSC_ADDPOINT);
		addAction("sortpoint.php?cpid=$currentplan", _MD_GWLOTO_PRG_DSC_SRTPOINT);
	}
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || 
	   isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS]) ||
	   isset($places['currentauth'][_GWLOTO_USERAUTH_CP_VIEW])) {
		addAction("viewplan.php?cpid=$currentplan",  _MD_GWLOTO_PRG_DSC_VIEWPLAN);
	}
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT]) || 
	   isset($places['currentauth'][_GWLOTO_USERAUTH_CP_TRANS])) {
		addAction("editplan.php?cpid=$currentplan",  _MD_GWLOTO_PRG_DSC_EDITPLAN);
	}
	if($show_select_action) {
		if(isset($places['alluserauth'][_GWLOTO_USERAUTH_CP_EDIT]) || 
		   isset($places['alluserauth'][_GWLOTO_USERAUTH_JB_EDIT])) {
				addAction("select.php?cpid=$currentplan",  _MD_GWLOTO_PRG_DSC_SELPLAN);
				$show_select_action=false;
		}
	}
}

// Things to do with a place
if(isset($currentplace) && $currentplace!=0) {
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_CP_EDIT])) {
		addAction("newplan.php?pid=$currentplace",  _MD_GWLOTO_PRG_DSC_ADDPLAN);
	}
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT]) || 
	   isset($places['currentauth'][_GWLOTO_USERAUTH_PL_TRANS])) {
		addAction("editplace.php?pid=$currentplace",  _MD_GWLOTO_PRG_DSC_EDITPLACE);
	}
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_PL_EDIT])) {
		addAction("newplace.php?pid=$currentplace",  _MD_GWLOTO_PRG_DSC_ADDPLACE);
	}
	if(isset($places['alluserauth'][_GWLOTO_USERAUTH_PL_EDIT])) {
		if($show_select_action) {
			addAction("select.php?pid=$currentplace",  _MD_GWLOTO_PRG_DSC_SELPLACE);
			$show_select_action=false;
		}
	}
}

// job list
if(isset($places['alluserauth'][_GWLOTO_USERAUTH_JB_VIEW]) || 
   isset($places['alluserauth'][_GWLOTO_USERAUTH_JB_EDIT])) {
	if(isset($currentplace))
		addAction("listjobs.php?pid=$currentplace",  _MD_GWLOTO_PRG_DSC_LISTJOBS);
	else
		addAction("listjobs.php",  _MD_GWLOTO_PRG_DSC_LISTJOBS);
}

// more things to do with a place
if(isset($currentplace) && $currentplace!=0) {
	if(isset($places['currentauth'][_GWLOTO_USERAUTH_MD_EDIT]) || 
	   isset($places['currentauth'][_GWLOTO_USERAUTH_MD_VIEW]) || 
	   isset($places['currentauth'][_GWLOTO_USERAUTH_MD_TRANS])) {
		addAction("listmedia.php?pid=$currentplace",  _MD_GWLOTO_PRG_DSC_MEDIA);
	}

	if(isset($places['currentauth'][_GWLOTO_USERAUTH_PL_ADMIN]) || 
	   isset($places['currentauth'][_GWLOTO_USERAUTH_PL_AUDIT])) {
		addAction("editauths.php?pid=$currentplace",  _MD_GWLOTO_PRG_DSC_EDITAUTHS);
	}
	if($myuserid!=0) {
		addAction("sethome.php?pid=$currentplace",  _MD_GWLOTO_PRG_DSC_SETHOME);
	}
}

if(isset($actions)) $xoopsTpl->assign('actions', $actions);
include 'clipboardform.php';

?>