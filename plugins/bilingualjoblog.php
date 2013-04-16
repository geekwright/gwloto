<?php
/**
* bilingualjoblog.php - demo jobprint plugin
* Generates pdf energy control log, with control point data listed in two languages
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
/*
	This is a plugin signature block for a gwloto plugin
	[plugin]
	[type]jobprint[/type]
	[link]jobprintshell.php[/link]
	[language_file]gw_jobprint.php[/language_file]
	[name]Bilingual Log Sheet[/name]
	[description]Energy control plan log sheet with control point info given in languages 0 and 1. PDF output.[/description]
	[/plugin]

*/

// load tcpdf library
$tcpdfPath=getTcpdfPath();
if (file_exists($tcpdfPath) ) {
	require_once($tcpdfPath);
} else die(_MD_GWLOTO_NEED_TCPDF);

$pdf=null;
// this is to facilitate translation of sequence (disonnect, inspect reconnect) data
$seqlabel=$seqoptions[$currentseq]['sort'];

// set the languages to use
$lang_one=0;
$lang_two=1;

function myBeginJobFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf;

	$pdf = new TCPDF('L', 'in', 'LETTER', true, 'UTF-8', false);
	$pdf->SetTitle($jobdata['job_name']);
	$pdf->SetFont('helvetica', '', 10, '', true);

	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	$pdf->SetMargins(0.2, 0.2, 0.2, true);
	$pdf->SetAutoPageBreak(true,0.2);

//	$pdf->AliasNbPages('{nb}');
//	$pdf->AliasNumPage('{pnb}');

	$pdf->SetDisplayMode('default', 'SinglePage', 'UseNone');

}

function myEndJobFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf;
	ob_clean();
	$zapus = array(' ', ',', '.', '/', '\\','<','>','|');
	$filename = str_replace($zapus, '_', $jobdata['job_name']);
	$pdf->Output('log_'.$filename.'.pdf', 'I'); //  I=send inline, D=force download
}

function myBeginStepFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf;
global $body,$language,$seqlabel;
global $lang_one,$lang_two;

$body='';

// get headings from language file previously loaded
$lid=$lang_one;
$LOG_TITLE=$GLOBALS['_GW_LOG_TITLE'][$lid];

$LOG_JOB_NAME=$GLOBALS['_GW_LOG_JOB_NAME'][$lid];
$LOG_JOB_PHASE=$GLOBALS['_GW_LOG_JOB_PHASE'][$lid];
$LOG_JOB_SEQUENCE=$GLOBALS['_GW_LOG_JOB_SEQUENCE'][$lid][$seqlabel];
//['seq_disconnect'], ['seq_inspection'], ['seq_reconnect']
$LOG_PAGE_NUMBERS=sprintf($GLOBALS['_GW_LOG_PAGE_NUMBERS'][$lid],$pdf->getAliasNumPage(),$pdf->getAliasNbPages());
$LOG_PLACE=$GLOBALS['_GW_LOG_PLACE'][$lid];
$LOG_WORKORDER=$GLOBALS['_GW_LOG_WORKORDER'][$lid];
$LOG_SUPERVISOR=$GLOBALS['_GW_LOG_SUPERVISOR'][$lid];
$LOG_STARTDATE=$GLOBALS['_GW_LOG_STARTDATE'][$lid];
$LOG_ENDDATE=$GLOBALS['_GW_LOG_ENDDATE'][$lid];
$LOG_DESCRIPTION=$GLOBALS['_GW_LOG_DESCRIPTION'][$lid];
$LOG_SIGNATURES=$GLOBALS['_GW_LOG_SIGNATURES'][$lid];
$LOG_PRINTDATE=$GLOBALS['_GW_LOG_PRINTDATE'][$lid];
$LOG_PLAN_NAME=$GLOBALS['_GW_LOG_PLAN_NAME'][$lid];
$LOG_STEP_NAME=$GLOBALS['_GW_LOG_STEP_NAME'][$lid];
$LOG_ASSIGNED_TO=$GLOBALS['_GW_LOG_ASSIGNED_TO'][$lid];
$LOG_REVIEWS=$GLOBALS['_GW_LOG_REVIEWS'][$lid];
$LOG_HAZARD_INV=$GLOBALS['_GW_LOG_HAZARD_INV'][$lid];
$LOG_REQUIRED_PPE=$GLOBALS['_GW_LOG_REQUIRED_PPE'][$lid];
$LOG_REQ_PERSONNEL=$GLOBALS['_GW_LOG_REQ_PERSONNEL'][$lid];
$LOG_REQUIREMENTS=$GLOBALS['_GW_LOG_REQUIREMENTS'][$lid];
$LOG_POINT_TOTAL=$GLOBALS['_GW_LOG_POINT_TOTAL'][$lid];
$LOG_LOCK_TOTAL=$GLOBALS['_GW_LOG_LOCK_TOTAL'][$lid];
$LOG_TAG_TOTAL=$GLOBALS['_GW_LOG_TAG_TOTAL'][$lid];
$LOG_POINT_INST=$GLOBALS['_GW_LOG_POINT_INST'][$lid];
$LOG_NORMAL_STATE=$GLOBALS['_GW_LOG_NORMAL_STATE'][$lid];
$LOG_LOCK_REQ=$GLOBALS['_GW_LOG_LOCK_REQ'][$lid];
$LOG_TAGS_REQ=$GLOBALS['_GW_LOG_TAGS_REQ'][$lid];
$LOG_INITIALS=$GLOBALS['_GW_LOG_INITIALS'][$lid];
$LOG_DATE_FORMAT=$GLOBALS['_GW_LOG_DATE_FORMAT'][$lid];
// end language file processing

$job_name=$jobdata['job_name'];
//$printed_date=$jobdata['printed_date'];
$printed_date=date($LOG_DATE_FORMAT, $jobdata['printed_date_raw']);
$job_workorder=$jobdata['job_workorder'];
$job_supervisor=$jobdata['job_supervisor'];
$job_startdate=$jobdata['job_startdate'];
$job_enddate=$jobdata['job_enddate'];
$job_description=nl2br($jobdata['job_description']);

$step_name=$jobstepdata[$language]['step_name'];
$display_job_step_status=$jobstepdata[$language]['display_job_step_status'];
$assigned_name=$jobstepdata[$language]['assigned_name'];
$pointcount=$jobstepdata[$language]['pointcount'];
$lockcount=$jobstepdata[$language]['lockcount'];
$tagcount=$jobstepdata[$language]['tagcount'];
$place_name=$jobstepdata[$language]['place_name'];
$place_hazard_inventory=nl2br($jobstepdata[$language]['place_hazard_inventory']);
$place_required_ppe=nl2br($jobstepdata[$language]['place_required_ppe']);
$cplan_name=$jobstepdata[$language]['cplan_name'];
$cplan_review=nl2br($jobstepdata[$language]['cplan_review']);
$hazard_inventory=nl2br($jobstepdata[$language]['hazard_inventory']);
$required_ppe=nl2br($jobstepdata[$language]['required_ppe']);
$authorized_personnel=nl2br($jobstepdata[$language]['authorized_personnel']);
$additional_requirements=nl2br($jobstepdata[$language]['additional_requirements']);

$placenamearray=$jobstepdata[$language]['fullplacename'];
$fullplacename='';
foreach($placenamearray as $v) {
	if($fullplacename!='') $fullplacename.=', ';
	$fullplacename.=$v;
}

$tbl = <<<EOD
<table border="1" width="100%" cellpadding="3" >
<thead>
 <tr style="background-color:#EEEEEE;color:#000000;">
  <td colspan="6" align="center"><h1>$LOG_TITLE</h1></td>
 </tr>
 <tr style="background-color:#EEEEEE;color:#000000;">
  <td width="50%"><h2>$LOG_JOB_NAME $job_name</h2></td>
  <td width="35%" colspan="3"><h2>$LOG_JOB_PHASE $LOG_JOB_SEQUENCE</h2></td>
  <td width="15%" colspan="2"> $LOG_PAGE_NUMBERS</td>
 </tr>
</thead>
<tr>
<td width="50%">
<table border="0">
<tr><td><b>$LOG_WORKORDER</b></td><td>$job_workorder</td></tr>
<tr><td><b>$LOG_SUPERVISOR</b></td><td>$job_supervisor</td></tr>
<tr><td><b>$LOG_STARTDATE</b></td><td>$job_startdate</td></tr>
<tr><td><b>$LOG_ENDDATE</b></td><td>$job_enddate</td></tr>
<tr><td colspan="2"><b>$LOG_DESCRIPTION</b><br />$job_description<br /></td></tr>
<tr><td colspan="2"><b>$LOG_SIGNATURES</b></td></tr>
<tr><td colspan="2"><br /><br /> ________  __________________________________</td></tr>
<tr><td colspan="2"><br /><br /> ________  __________________________________</td></tr>
<tr><td colspan="2"><br /><br /> ________  __________________________________</td></tr>
<tr><td colspan="2"><br /><br /> ________  __________________________________</td></tr>
<tr><td colspan="2"><br /><br /> ________  __________________________________</td></tr>
<tr><td colspan="2"><br /><br /> ________  __________________________________<br /><br /></td></tr>
<tr><td><b>$LOG_PRINTDATE</b></td><td>$printed_date</td></tr>
</table>
</td>
<td width="50%" colspan="3">
<table border="0">
<tr><td><b>$LOG_PLAN_NAME</b></td><td>$cplan_name</td></tr>
<tr><td><b>$LOG_STEP_NAME</b></td><td>$step_name</td></tr>
<tr><td><b>$LOG_PLACE</b></td><td>$fullplacename</td></tr>
<tr><td><b>$LOG_ASSIGNED_TO</b></td><td>$assigned_name</td></tr>
<tr><td colspan="2"><b>$LOG_REVIEWS</b><br />$cplan_review<br /></td></tr>
<tr><td colspan="2"><br /><b>$LOG_HAZARD_INV</b><br />$hazard_inventory<br /></td></tr>
<tr><td colspan="2"><br /><b>$LOG_REQUIRED_PPE</b><br />$required_ppe<br /></td></tr>
<tr><td colspan="2"><br /><b>$LOG_REQ_PERSONNEL</b><br />$authorized_personnel<br /></td></tr>
<tr><td colspan="2"><br /><b>$LOG_REQUIREMENTS</b><br />$additional_requirements<br /></td></tr>
<tr><td><b>$LOG_POINT_TOTAL</b></td><td>$pointcount</td></tr>
<tr><td><b>$LOG_LOCK_TOTAL</b></td><td>$lockcount</td></tr>
<tr><td><b>$LOG_TAG_TOTAL</b></td><td>$tagcount</td></tr>
</table>
</td>
</tr>
</table>
EOD;

	$pdf->AddPage();
	$pdf->writeHTML($tbl, true, false, false, false, '');

	$pdf->AddPage();

$tbl = <<<EOD
<table border="1" width="100%" cellpadding="2" >
<thead>
 <tr style="background-color:#EEEEEE;color:#000000;">
  <td colspan="6" align="center"><h1>$LOG_TITLE</h1></td>
 </tr>
 <tr style="background-color:#EEEEEE;color:#000000;">
  <td width="50%"><h2>$LOG_JOB_NAME $job_name</h2></td>
  <td width="35%" colspan="3"><h2>$LOG_JOB_PHASE $LOG_JOB_SEQUENCE</h2></td>
  <td width="15%" colspan="2"> $LOG_PAGE_NUMBERS</td>
 </tr>
 <tr>
  <td width="50%"><h2>$cplan_name</h2></td>
  <td width="50%" colspan="5"><h2>$LOG_PLACE  $place_name</h2></td>
 </tr>
 <tr>
  <td width="50%"><b>$LOG_POINT_INST</b></td>
  <td width="15%"><b>$LOG_NORMAL_STATE</b></td>
  <td width="15%"><b>$LOG_JOB_SEQUENCE</b></td>
  <td width="5%"><b>$LOG_LOCK_REQ</b></td>
  <td width="5%"><b>$LOG_TAGS_REQ</b></td>
  <td width="10%"><b>$LOG_INITIALS</b></td>
 </tr>
</thead>
EOD;

	$body.=$tbl;
}

function myEndStepFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf;
global $body;
	$body.='</table>';
	$pdf->writeHTML($body, true, false, false, false, '');
//	$pdf->endPage();
	$body='';
}

function myEachPointFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf;
global $body, $language;
global $lang_one,$lang_two;

$locks_required=$pointdata[$lang_one]['locks_required'];
$tags_required=$pointdata[$lang_one]['tags_required'];
// repeat in second language if different
$cpoint_name=$pointdata[$lang_one]['cpoint_name'];
if($pointdata[$lang_one]['cpoint_name']!=$pointdata[$lang_two]['cpoint_name']) $cpoint_name.='<br />'.$pointdata[$lang_two]['cpoint_name']; 
$reconnect_state=$pointdata[$lang_one]['reconnect_state'];
if($pointdata[$lang_one]['reconnect_state']!=$pointdata[$lang_two]['reconnect_state']) $reconnect_state.='<br />'.$pointdata[$lang_two]['reconnect_state']; 
$instructions=nl2br($pointdata[$lang_one]['instructions']);
if($pointdata[$lang_one]['instructions']!=$pointdata[$lang_two]['instructions']) $instructions.='<br /><br />'.nl2br($pointdata[$lang_two]['instructions']); 
$state=$pointdata[$lang_one]['state'];
if($pointdata[$lang_one]['state']!=$pointdata[$lang_two]['state']) $state.='<br />'.$pointdata[$lang_two]['state']; 

$initials="<br />";
for ($i = 1; $i <= max($locks_required,$tags_required); $i++) {
	$initials.="<br />__________";
}

$tbl = <<<EOD
 <tr>
  <td width="50%"><b>$cpoint_name</b><br />$instructions</td>
  <td width="15%">$reconnect_state</td>
  <td width="15%">$state</td>
  <td width="5%">$locks_required</td>
  <td width="5%">$tags_required</td>
  <td width="10%">$initials </td>
 </tr>
EOD;

	$body.=$tbl;
}

$myPrint=new gwlotoPrintJob($currentjob, $currentplan, $currentseq, array($lang_one,$lang_two), 'myEachPointFunc', 'myBeginStepFunc', 'myEndStepFunc', 'myBeginJobFunc', 'myEndJobFunc');

$body='';
$myPrint->doPrint();

?>
