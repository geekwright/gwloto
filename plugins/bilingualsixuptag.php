<?php
/**
* bilingualsixuptag.php - demo jobprint plugin
* Generates pdf lockout tags in two languages
* Tags print six to a page, alternating language each page so that when
* printed in duplex mode, each tag has altenate language on flip side
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
    [name]Bilingual Six to a Page Tags[/name]
    [description]Six tags to the page, duplex printing, language 0 on front, language 1 on back. PDF output.[/description]
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

$tagaltoffsets=array(
0=>$tagoffsets[2],
1=>$tagoffsets[1],
2=>$tagoffsets[0],
3=>$tagoffsets[5],
4=>$tagoffsets[4],
5=>$tagoffsets[3]);

$toindex=0;
$tomax=5;

// set the languages to use
$lang_one=0;
$lang_two=1;

$lang_one_page=0;
$lang_two_page=0;

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
    global $tagoffsets, $toindex, $tomax, $tagaltoffsets, $lang_one_page, $lang_two_page;
    global $lang_one,$lang_two;

    $copies=intval($pointdata[$language]['tags_required']);
    $tagtotal=intval($jobstepdata[$language]['tagcount']);

    while ($copies>0) {
        --$copies;
        ++$tagcnt;

        if ($toindex==0) {
            $pdf->AddPage();
            $lang_one_page=$pdf->getPage();

            $pdf->AddPage();
            $lang_two_page=$pdf->getPage();
        }

    
        if ($firstinstep) {
            $firstinstep=false;
        // need to have a page to define a bookmark, so firstinstep is used as indicator
        // only add if we have more than one step
        if ($jobdata['stepcount']>1) {
            $pdf->setPage($lang_one_page);
            $pdf->Bookmark($jobstepdata[$lang_one]['cplan_name'], $level = 0);
            $pdf->setPage($lang_two_page);
            $pdf->Bookmark($jobstepdata[$lang_two]['cplan_name'], $level = 0);
        }
        }

        $x=$tagoffsets[$toindex]['x'];
        $y=$tagoffsets[$toindex]['y'];
        $pdf->setPage($lang_one_page);
        layoutTag($jobdata, $jobstepdata, $pointdata, $x, $y, $lang_one);

        $x=$tagaltoffsets[$toindex]['x'];
        $y=$tagaltoffsets[$toindex]['y'];
        $pdf->setPage($lang_two_page);
        layoutTag($jobdata, $jobstepdata, $pointdata, $x, $y, $lang_two);

        ++$toindex;
        if ($toindex>$tomax) {
            $toindex=0;
        }
    }
}

$myPrint=new gwlotoPrintJob($currentjob, $currentplan, $currentseq, array($lang_one, $lang_two), 'myEachPointFunc', 'myBeginStepFunc', 'myEndStepFunc', 'myBeginJobFunc', 'myEndJobFunc');

$myPrint->doPrint();
