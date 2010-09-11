<?php
/**
* jobstatus.php - translation tables for database enum columns
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

$jobstatus=array();
$jobstatus['planning']=_MD_GWLOTO_JOB_STATUS_PLANNING;
$jobstatus['active']=_MD_GWLOTO_JOB_STATUS_ACTIVE;
$jobstatus['complete']=_MD_GWLOTO_JOB_STATUS_COMPLETE;
$jobstatus['canceled']=_MD_GWLOTO_JOB_STATUS_CANCELED;

$stepstatus=array();

// stepseqop gives index into $seqoptions for this step
// this must agree with order established in seqoptions.php
$stepseqop=array();
$i=0;

$stepstatus['planning']=_MD_GWLOTO_JOBSTEP_STATUS_PLANNING;

$stepstatus['disconnecting']=_MD_GWLOTO_JOBSTEP_STATUS_WIP_DISC;
$stepstatus['disconnected']=_MD_GWLOTO_JOBSTEP_STATUS_DISC;
$stepseqop['disconnecting']=$i;
$stepseqop['disconnected']=$i;

if($xoopsModuleConfig['show_inspect']) {
	++$i;
	$stepstatus['inspecting']=_MD_GWLOTO_JOBSTEP_STATUS_WIP_INSP;
	$stepstatus['inspected']=_MD_GWLOTO_JOBSTEP_STATUS_INSP;
	$stepseqop['inspecting']=$i;
	$stepseqop['inspected']=$i;
}

if($xoopsModuleConfig['show_reconnect']) {
	++$i;
	$stepstatus['reconnecting']=_MD_GWLOTO_JOBSTEP_STATUS_WIP_RECON;
	$stepstatus['reconnected']=_MD_GWLOTO_JOBSTEP_STATUS_RECON;
	$stepseqop['reconnecting']=$i;
	$stepseqop['reconnected']=$i;
}

$stepstatus['complete']=_MD_GWLOTO_JOBSTEP_STATUS_COMPLETE;
$stepstatus['canceled']=_MD_GWLOTO_JOBSTEP_STATUS_CANCELED;

?>