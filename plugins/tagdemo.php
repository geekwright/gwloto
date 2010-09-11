<?php
/**
* tagdemo.php - demo jobprint plugin
* Generates pdf lockout tags
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
	[name]Tag Demo[/name]
	[description]Tags on 3x6.25 inch form. PDF output[/description]
	[/plugin]

*/

// load tcpdf library
$tcpdfPath=getTcpdfPath();
if (file_exists($tcpdfPath) ) {
	require_once($tcpdfPath);
} else die(_MD_GWLOTO_NEED_TCPDF);

$pdf=null;
$tagcnt=0;
$firstinstep=false;

$xmedia=3.0;
$ymedia=6.25;

$xoffset=0.0;
$yoffset=0.0;

function myBeginJobFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf;
global $xmedia, $ymedia, $xoffset, $yoffset;

	$pdf = new TCPDF('P', 'in', array($xmedia, $ymedia), true, 'UTF-8', false);
	$pdf->SetTitle($jobdata['job_name']);
	$pdf->SetFont('helvetica', '', 10, '', true);

	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	$pdf->SetMargins(0.1+$xoffset, 0.1+$yoffset, 0.1+$xoffset, true);
	$pdf->SetAutoPageBreak(false,0);

	$pdf->AliasNbPages('{nb}');
	$pdf->AliasNumPage('{pnb}');

	$preferences = array(
    		'PageLayoutSinglePage' => true,
    		'PrintScaling' => 'None'
		);
//	$pdf->setViewerPreferences($preferences);
	$pdf->SetDisplayMode('default', 'SinglePage', 'UseNone');
}

function myEndJobFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf;
	ob_clean();
	$zapus = array(' ', ',', '.', '/', '\\','<','>','|');
	$filename = str_replace($zapus, '_', $jobdata['job_name']);
	$pdf->Output('tags_'.$filename.'.pdf', 'D'); //  I=send inline, D=force download
}

function myBeginStepFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf,$tagcnt,$firstinstep;
	$tagcnt=0;
	$firstinstep=true;
}

function myEndStepFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf;
}

function myEachPointFunc($jobdata,$jobstepdata,$pointdata) {
global $pdf,$tagcnt,$firstinstep, $language;
global $xmedia, $ymedia, $xoffset, $yoffset;

	// get headings from language file
	$TAG_DANGER_IMAGE=$GLOBALS['_GW_TAG_DANGER_IMAGE'][$language];
	$TAG_DO_NOT_OPERATE=$GLOBALS['_GW_TAG_DO_NOT_OPERATE'][$language];
	$TAG_LOCKED_OUT=$GLOBALS['_GW_TAG_LOCKED_OUT'][$language];
	$TAG_DISCONNECT=$GLOBALS['_GW_TAG_DISCONNECT'][$language];
	$TAG_JOB_NAME=$GLOBALS['_GW_TAG_JOB_NAME'][$language];
	$TAG_STEP_NAME=$GLOBALS['_GW_TAG_STEP_NAME'][$language];
	$TAG_PLACE=$GLOBALS['_GW_TAG_PLACE'][$language];
	$TAG_WORKORDER=$GLOBALS['_GW_TAG_WORKORDER'][$language];
	$TAG_SUPERVISOR=$GLOBALS['_GW_TAG_SUPERVISOR'][$language];
	$TAG_END_DATE=$GLOBALS['_GW_TAG_END_DATE'][$language];
	$TAG_PLACED_BY=$GLOBALS['_GW_TAG_PLACED_BY'][$language];
	$TAG_PRINTED_DATE=$GLOBALS['_GW_TAG_PRINTED_DATE'][$language];
	$TAG_DATE_FORMAT=$GLOBALS['_GW_TAG_DATE_FORMAT'][$language];

$copies=intval($pointdata[$language]['tags_required']);
$tagtotal=intval($jobstepdata[$language]['tagcount']);

while($copies>0) {
	--$copies;
	++$tagcnt;
	$pdf->AddPage();
	if($firstinstep) {
		$firstinstep=false;
		// need to have a page to define a bookmark, so firstinstep is used as indicator
		// only add if we have more than one step
		if ($jobdata['stepcount']>1) $pdf->Bookmark($jobstepdata[$language]['cplan_name'], $level = 0);
	}

	$pdf->Image($TAG_DANGER_IMAGE, 0.1+$xoffset, 0.7+$yoffset, 2.8, 1.0, 'png', '', 'N', true);

	$pdf->SetXY(0.2+$xoffset, 1.75+$yoffset );
	$pdf->SetFont('', 'B', 26, '', true);
	$pdf->Cell( 2.6, 0.5, $TAG_DO_NOT_OPERATE, 0, 1, 'C');

	$pdf->SetXY(0.2+$xoffset, 2.2+$yoffset );
	$pdf->SetFont('', 'B', 16, '', true);
	$pdf->Cell( 2.6, 0.3, $TAG_LOCKED_OUT, 0, 1, 'C');


	$pdf->SetFont('', '', 10, '', true);
	$pdf->Line( 0.2+$xoffset, 2.6+$yoffset, 2.8+$xoffset, 2.6+$yoffset);

	$html  = '<b>'.$pointdata[$language]['cpoint_name'].'</b><br><br>';
	$html .= '<b>'.$TAG_DISCONNECT.'</b>   '.$pointdata[$language]['disconnect_state'].'<br>';
	$html .= '<font size="-2">'.$pointdata[$language]['disconnect_instructions'].'</font><br>';
	// Print text using writeHTMLCell()
	$pdf->writeHTMLCell($w=0, $h=0, $x=0.1+$xoffset, $y=2.65+$yoffset, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

	$html  = '<b>'.$TAG_JOB_NAME.'</b>  '.$jobdata['job_name'].'<br>';
	$html .= '<b>'.$TAG_STEP_NAME.'</b>  '.$jobstepdata[$language]['cplan_name'].'<br>';
	$html .= '<b>'.$TAG_PLACE.'</b>  '.$jobstepdata[$language]['place_name'].'<br>';
	$html .= '<b>'.$TAG_WORKORDER.'</b>   '.$jobdata['job_workorder'].'<br>';
	$html .= '<b>'.$TAG_SUPERVISOR.'</b>   '.$jobdata['job_supervisor'].'<br>';
	$html .= '<b>'.$TAG_END_DATE.'</b>    '.$jobdata['job_enddate'].'<br>';
	$html .= '<b>'.$TAG_PLACED_BY.'</b>   '.$jobstepdata[$language]['assigned_name'].'<br>';

	// Print text using writeHTMLCell()
	$pdf->writeHTMLCell($w=0, $h=0, $x=$xoffset, $y=4.8+$yoffset, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

	$html = "<font size=\"-2\">$tagcnt/$tagtotal</font>";
	$pdf->writeHTMLCell($w=0, $h=0, 2.5+$xoffset, 6.0+$yoffset, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	}
}

$myPrint=new gwlotoPrintJob($currentjob, $currentplan, $currentseq, $language, 'myEachPointFunc', 'myBeginStepFunc', 'myEndStepFunc', 'myBeginJobFunc', 'myEndJobFunc');

$myPrint->doPrint();

?>