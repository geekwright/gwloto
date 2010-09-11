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
	global $xoopsDB,$xoopsUser;
	$jobs=array();

	$ourdir=basename( dirname( dirname( __FILE__ ) ) ) ;

	$output='';

	$myuserid=0;
	if($xoopsUser) {
		$myuserid = $xoopsUser->getVar('uid');
	}

	$orderby = 'steps.last_changed_on';
	if(strcasecmp($options[0],'desc')==0) $orderby = 'steps.last_changed_on desc';

	$sql ='SELECT job_step_id, job_name, job_workorder, step_name, steps.last_changed_on ';
	$sql.='FROM '.$xoopsDB->prefix('gwloto_job');
	$sql.=', '.$xoopsDB->prefix('gwloto_job_steps').' steps ';
	$sql.=" WHERE assigned_uid = $myuserid ";
	$sql.=' AND job = job_id ';
	$sql.=" AND job_status IN ('planning','active')";
	$sql.=" AND job_step_status NOT IN ('complete','canceled')";
	$sql.=" ORDER BY $orderby ";

	$result = $xoopsDB->query($sql);
		if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$jsid=$myrow['job_step_id'];
			$jn=$myrow['job_name'];
			$sn=$myrow['step_name'];
			$wo=$myrow['job_workorder'];
			$link=XOOPS_URL."/modules/$ourdir/viewstep.php?jsid=$jsid";
			$txt=$jn;
			if($sn!='') $txt.=' - '.$sn;
			elseif($wo!='') $txt.=' - '.$wo;
			$output.='<li><a href="'.$link.'">'.$txt.'</a></li>';
		}
	}

	$block=null;
	if($output!='') $block['output']='<ul>'.$output.'</ul>';
	return $block;
}

function b_gwloto_assigned_block_edit($options) {
	// access in block as $options[0];
	$form = _MB_GWLOTO_ASSIGNED_BLOCK_OPTION.": <input type='text' value='".$options[0]."'id='options[0]' name='options[0]' />";

	return $form;
}

?>