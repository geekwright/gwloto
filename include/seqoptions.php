<?php
/**
* seqoptions.php - build table of sequence/phase related data
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

if (!defined("XOOPS_ROOT_PATH")) die("Root path not defined");

$seqoptions=array();
$i=0;
$seqoptions[$i]['label']=_MD_GWLOTO_SORTPOINT_SEQ_DISCON; // display name of this phase
$seqoptions[$i]['sort']='seq_disconnect'; // order by column for gwloto_cpoint
$seqoptions[$i]['state']='disconnect_state'; // applicable state column from gwloto_cpoint_detail
$seqoptions[$i]['instructions']='disconnect_instructions'; // applicable instruction column from gwloto_cpoint_detail

if($xoopsModuleConfig['show_inspect']) {
	++$i;
	$seqoptions[$i]['label']=_MD_GWLOTO_SORTPOINT_SEQ_INSPECT;
	$seqoptions[$i]['sort']='seq_inspection';
	$seqoptions[$i]['state']='inspection_state';
	$seqoptions[$i]['instructions']='inspection_instructions';
}

if($xoopsModuleConfig['show_reconnect']) {
	++$i;
	$seqoptions[$i]['label']=_MD_GWLOTO_SORTPOINT_SEQ_RECON;
	$seqoptions[$i]['sort']='seq_reconnect';
	$seqoptions[$i]['state']='reconnect_state';
	$seqoptions[$i]['instructions']='reconnect_instructions';
}

$currentseq=0;
if(isset($_GET['seq'])) $currentseq=intval($_GET['seq']);
if(isset($_POST['seq'])) $currentseq=intval($_POST['seq']);
if($currentseq<0 || $currentseq>$i) $currentseq=0;
?>