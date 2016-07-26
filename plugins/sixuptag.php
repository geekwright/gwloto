<?php
/**
* sixuptag.php - demo jobprint plugin
* Generates pdf lockout tags six to a page
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

if (!defined("XOOPS_ROOT_PATH")) {
    die("Root path not defined");
}
/*
    This is a plugin signature block for a gwloto plugin
    [plugin]
    [type]jobprint[/type]
    [link]jobprintshell.php[/link]
    [language_file]gw_jobprint.php[/language_file]
    [name]Six to a Page Tags Demo[/name]
    [description]Prints six tags on an 8.5x11 inch sheet. PDF output.[/description]
    [/plugin]

*/

// load tcpdf library
$tcpdfPath=getTcpdfPath();
if (file_exists($tcpdfPath)) {
    require_once($tcpdfPath);
} else {
    die(_MD_GWLOTO_NEED_TCPDF);
}

$pdf=null;
$tagcnt=0;
$tagtotal=0;
$firstinstep=false;

$xoffset=0.1;
$yoffset=0.1;

$tagx=2.833;
$tagy=5.5;

$tagoffsets=array(
0=>array('x'=>$xoffset,'y'=>$yoffset),
1=>array('x'=>$xoffset+$tagx,'y'=>$yoffset),
2=>array('x'=>$xoffset+$tagx+$tagx,'y'=>$yoffset),
3=>array('x'=>$xoffset,'y'=>$yoffset+$tagy),
4=>array('x'=>$xoffset+$tagx,'y'=>$yoffset+$tagy),
5=>array('x'=>$xoffset+$tagx+$tagx,'y'=>$yoffset+$tagy));

$toindex=0;
$tomax=5;

function myBeginJobFunc($jobdata, $jobstepdata, $pointdata)
{
    global $pdf;
    global $xmedia, $ymedia, $xoffset, $yoffset;

    $pdf = new TCPDF('P', 'in', 'LETTER', true, 'UTF-8', false);
    $pdf->SetTitle($jobdata['job_name']);
    $pdf->SetFont('helvetica', '', 10, '', true);

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $pdf->SetMargins(0.1, 0.1, 0.1, true);
    $pdf->SetAutoPageBreak(false, 0);

    $pdf->SetDisplayMode('default', 'SinglePage', 'UseNone');
}

function myEndJobFunc($jobdata, $jobstepdata, $pointdata)
{
    global $pdf;
    $zapus = array(' ', ',', '.', '/', '\\','<','>','|');
    $filename = str_replace($zapus, '_', $jobdata['job_name']);
    $pdf->Output('tags_'.$filename.'.pdf', 'I'); //  I=send inline, D=force download
}

function myBeginStepFunc($jobdata, $jobstepdata, $pointdata)
{
    global $pdf,$tagcnt,$firstinstep;
    $tagcnt=0;
    $firstinstep=true;
}

function myEndStepFunc($jobdata, $jobstepdata, $pointdata)
{
    global $pdf;
}

function layoutTag($jobdata, $jobstepdata, $pointdata, $xoffset, $yoffset, $lid)
{
    global $pdf,$tagcnt,$tagtotal;

    // get headings from language file
    $TAG_DANGER_IMAGE=$GLOBALS['_GW_TAG_DANGER_IMAGE'][$lid];
    $TAG_DO_NOT_OPERATE=$GLOBALS['_GW_TAG_DO_NOT_OPERATE'][$lid];
    $TAG_LOCKED_OUT=$GLOBALS['_GW_TAG_LOCKED_OUT'][$lid];
    $TAG_DISCONNECT=$GLOBALS['_GW_TAG_DISCONNECT'][$lid];
    $TAG_JOB_NAME=$GLOBALS['_GW_TAG_JOB_NAME'][$lid];
    $TAG_STEP_NAME=$GLOBALS['_GW_TAG_STEP_NAME'][$lid];
    $TAG_PLACE=$GLOBALS['_GW_TAG_PLACE'][$lid];
    $TAG_WORKORDER=$GLOBALS['_GW_TAG_WORKORDER'][$lid];
    $TAG_SUPERVISOR=$GLOBALS['_GW_TAG_SUPERVISOR'][$lid];
    $TAG_END_DATE=$GLOBALS['_GW_TAG_END_DATE'][$lid];
    $TAG_PLACED_BY=$GLOBALS['_GW_TAG_PLACED_BY'][$lid];
    $TAG_PRINTED_DATE=$GLOBALS['_GW_TAG_PRINTED_DATE'][$lid];
    $TAG_DATE_FORMAT=$GLOBALS['_GW_TAG_DATE_FORMAT'][$lid];


    $pdf->Image($TAG_DANGER_IMAGE, 0.1+$xoffset, 1.0+$yoffset, 2.40, 0.84, 'png', '', 'N', true);

    $pdf->SetXY(0.1+$xoffset, 1.85+$yoffset);
    $pdf->SetFont('', 'B', 22, '', true);
    $pdf->Cell(2.4, 0.5, $TAG_DO_NOT_OPERATE, 0, 1, 'C');

    $pdf->SetXY(0.1+$xoffset, 2.3+$yoffset);
    $pdf->SetFont('', 'B', 14, '', true);
    $pdf->Cell(2.4, 0.3, $TAG_LOCKED_OUT, 0, 1, 'C');

    $pdf->SetFont('', '', 10, '', true);
    $pdf->Line(0.1+$xoffset, 2.6+$yoffset, 2.5+$xoffset, 2.6+$yoffset);

    $html  = '<b>'.$pointdata[$lid]['cpoint_name'].'</b><br><br>';
    $html .= '<b>'.$TAG_DISCONNECT.'</b>   '.$pointdata[$lid]['disconnect_state'].'<br><br>';
    $html .= '<b>'.$TAG_JOB_NAME.'</b>  '.$jobdata['job_name'].'<br>';
    $html .= '<b>'.$TAG_STEP_NAME.'</b>  '.$jobstepdata[$lid]['cplan_name'].'<br>';
    $html .= '<b>'.$TAG_PLACE.'</b>  '.$jobstepdata[$lid]['place_name'].'<br>';
    $html .= '<b>'.$TAG_WORKORDER.'</b>   '.$jobdata['job_workorder'].'<br>';
    $html .= '<b>'.$TAG_SUPERVISOR.'</b>   '.$jobdata['job_supervisor'].'<br>';
    $html .= '<b>'.$TAG_END_DATE.'</b>    '.$jobdata['job_enddate'].'<br>';
    $html .= '<b>'.$TAG_PLACED_BY.'</b>   '.$jobstepdata[$lid]['assigned_name'].'<br>';
    $html .= '<b>'.$TAG_PRINTED_DATE.'</b>  '.date($TAG_DATE_FORMAT);

    $pdf->writeHTMLCell($w=2.4, $h=0, $x=0.1+$xoffset, $y=2.7+$yoffset, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

    $html = "<font size=\"-2\">$tagcnt/$tagtotal</font>";
    $pdf->writeHTMLCell($w=0.4, $h=0, 2.0+$xoffset, 0.25+$yoffset, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='R', $autopadding=true);
}

function myEachPointFunc($jobdata, $jobstepdata, $pointdata)
{
    global $pdf,$tagcnt,$tagtotal,$firstinstep, $language;
    global $xoffset, $yoffset;
    global $tagoffsets, $toindex, $tomax;

    $copies=intval($pointdata[$language]['tags_required']);
    $tagtotal=intval($jobstepdata[$language]['tagcount']);

    while ($copies>0) {
        --$copies;
        ++$tagcnt;

        if ($toindex==0) {
            $pdf->AddPage();
        }
    
        if ($firstinstep) {
            $firstinstep=false;
        // need to have a page to define a bookmark, so firstinstep is used as indicator
        // only add if we have more than one step
        if ($jobdata['stepcount']>1) {
            $pdf->Bookmark($jobstepdata[$language]['cplan_name'], $level = 0);
        }
        }

        $x=$tagoffsets[$toindex]['x'];
        $y=$tagoffsets[$toindex]['y'];

        layoutTag($jobdata, $jobstepdata, $pointdata, $x, $y, $language);

        ++$toindex;
        if ($toindex>$tomax) {
            $toindex=0;
        }
    }
}

$myPrint=new gwlotoPrintJob($currentjob, $currentplan, $currentseq, $language, 'myEachPointFunc', 'myBeginStepFunc', 'myEndStepFunc', 'myBeginJobFunc', 'myEndJobFunc');

$myPrint->doPrint();
