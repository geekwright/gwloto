<?php
/**
* menupick.php - process menu selection
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

include '../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'gwloto_index.html';
include(XOOPS_ROOT_PATH.'/header.php');

function cleanMenuAction($redir)
{
    $qmark=strpos($redir, '?');
    if ($qmark) {
        $script=substr($redir, 0, $qmark);
        $parms=substr($redir, $qmark+1);
    } else {
        $script=$redir;
        $parms='';
    }
    $validscripts=array(
     'index.php' => 1

    ,'editauths.php' => 1
    ,'select.php' => 1
    ,'sethome.php' => 1

    ,'viewjob.php' => 1
    ,'newjob.php' => 1
    ,'printjob.php' => 1
    ,'listjobs.php' => 1

    ,'viewstep.php' => 1

    ,'listmedia.php' => 1

    ,'editplan.php' => 1
    ,'newplan.php' => 1
    ,'viewplan.php' => 1

    ,'editpoint.php' => 1
    ,'newpoint.php' => 1
    ,'sortpoint.php' => 1
    ,'viewpoint.php' => 1

    ,'editplace.php' => 1
    ,'newplace.php' => 1
    );

    $validparms=array(
     'pid' => 1
    ,'cpid' => 1
    ,'ptid' => 1
    ,'mid' => 1
    );


    if (!isset($validscripts[$script])) {
        return false;
    }

    $retredir=$script;

    if ($parms!='') {
        $parmarray=array();
        $outparm=array();

        parse_str($parms, $parmarray);
        foreach ($validparms as $i => $v) {
            if (isset($parmarray[$i])) {
                if ($v==1) {
                    $outparm[$i]=intval($parmarray[$i]);
                }
            }
        }

        $cnt=1;
        $redirparms='';
        foreach ($outparm as $i => $v) {
            if ($cnt==1) {
                $redirparms.="?$i=$v";
            } else {
                $redirparms.="&$i=$v";
            }
            ++$cnt;
        }
        $retredir=$script.$redirparms;
    }
    return $retredir;
}

if (isset($_POST['menuaction'])) {
    $redir = $_POST['menuaction'];

    $cleanredir=cleanMenuAction($redir);

    $dirname=$xoopsModule->getInfo('dirname');
    header('Location: ' . XOOPS_URL . '/modules/'.$dirname.'/'. $cleanredir);
    exit;
}

redirect_header('index.php', 3, _MD_GWLOTO_MSG_BAD_PARMS);

//include(XOOPS_ROOT_PATH.'/footer.php');
;
