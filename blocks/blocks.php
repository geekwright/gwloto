<?php
/**
* blocks.php - blocks
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

if (!defined('XOOPS_ROOT_PATH')){ die(); }

function b_gwloto_assigned_block_show($options) {
	global $xoopsDB,$xoopsUser,$xoopsConfig;
	$jobs=array();

	$ourdir=basename( dirname( dirname( __FILE__ ) ) ) ;
	$modpath=XOOPS_ROOT_PATH . '/modules/' . $ourdir;

	if ( file_exists( $modpath . '/language/' . $xoopsConfig['language'] . '/main.php' ) ) {
		include_once $modpath . '/language/' . $xoopsConfig['language'] . '/main.php';
	}elseif ( file_exists( $modpath . '/language/english/main.php' ) ) {
		include_once $modpath . '/language/english/main.php';
	}
	include_once $modpath . '/include/jobstatus.php';

	$output='';

	$myuserid=0;
	if($xoopsUser) {
		$myuserid = $xoopsUser->getVar('uid');
	}

	$orderby = 'steps.last_changed_on';
	if(strcasecmp($options[0],'desc')==0) $orderby = 'steps.last_changed_on desc';

	$sql ='SELECT job_step_id, job_name, job_workorder, job_description, step_name, job_step_status, steps.last_changed_on ';
	$sql.='FROM '.$xoopsDB->prefix('gwloto_job');
	$sql.=', '.$xoopsDB->prefix('gwloto_job_steps').' steps ';
	$sql.=" WHERE assigned_uid = $myuserid ";
	$sql.=' AND job = job_id ';
	$sql.=" AND job_status IN ('planning','active')";
	$sql.=" AND job_step_status NOT IN ('complete','canceled')";
	$sql.=" ORDER BY $orderby ";

	$limit=intval($options[1]);
	if($limit<1) $limit=5;
	$block=null;

	$result = $xoopsDB->query($sql,$limit,0);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$jsid=$myrow['job_step_id'];
			$block[$jsid]['job_step_id']=$jsid;

			$block[$jsid]['job_name']=htmlspecialchars($myrow['job_name'], ENT_QUOTES);
			$block[$jsid]['job_workorder']=htmlspecialchars($myrow['job_workorder'], ENT_QUOTES);
			$block[$jsid]['step_name']=htmlspecialchars($myrow['step_name'], ENT_QUOTES);
			$block[$jsid]['job_description']=htmlspecialchars($myrow['job_description'], ENT_QUOTES);

			$block[$jsid]['link']=XOOPS_URL."/modules/$ourdir/viewstep.php?jsid=$jsid";
			$block[$jsid]['status']=$stepstatus[$myrow['job_step_status']];
		}
	}
	return $block;
}

function b_gwloto_assigned_block_edit($options) {
	// access in block as $options[0];
	$form = _MB_GWLOTO_ASSIGNED_BLOCK_OPTION_1.": <input type='text' value='".$options[0]."'id='options[0]' name='options[0]' />";
	$form.= '<br />'._MB_GWLOTO_ASSIGNED_BLOCK_OPTION_2.": <input type='text' value='".$options[1]."'id='options[1]' name='options[1]' />";

	return $form;
}

?>