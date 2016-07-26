<?php
/**
* jobdump.php - demo jobprint plugin
* html format dump of data arrays as an illustration of what is
* available for use in custom plugins
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
    [name]Debugging Aid[/name]
    [description]This does a dump of raw job data for testing[/description]
    [/plugin]

*/

// these are the callback functions supplied to gwlotoPrintJob object

function myBeginJobFunc($jobdata, $jobstepdata, $pointdata)
{
    global $body;
    $body.='<pre>$jobdata='.print_r($jobdata, true).'</pre>';
}

function myEndJobFunc($jobdata, $jobstepdata, $pointdata)
{
}

function myBeginStepFunc($jobdata, $jobstepdata, $pointdata)
{
    global $body;
    $body.='<pre>$jobstepdata='.print_r($jobstepdata, true).'</pre>';
}

function myEndStepFunc($jobdata, $jobstepdata, $pointdata)
{
}

function myEachPointFunc($jobdata, $jobstepdata, $pointdata)
{
    global $body;
    $body.='<pre>$pointdata='.print_r($pointdata, true).'</pre>';
}

// jobprintshell has set up the parameters and included the gwlotoPrintJob class file
// Here we supply our callbacks for the new gwlotoPrintJob object
$myPrint=new gwlotoPrintJob($currentjob, $currentplan, $currentseq, $language, 'myEachPointFunc', 'myBeginStepFunc', 'myEndStepFunc', 'myBeginJobFunc', 'myEndJobFunc');

// doPrint method will present job data by invoking callbacks
$body='';
$myPrint->doPrint();
echo $body;
