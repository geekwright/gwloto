<?php
/**
* index.php - admin page for about and configuration messages
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

include 'header.php';

    if ($xoop25plus) {
        echo $moduleAdmin->addNavigation('index.php') ;
        $welcome=_AD_GW_ADMENU_WELCOME;
        $moduleAdmin->addInfoBox($welcome);
        $moduleAdmin->addInfoBoxLine($welcome, _AD_GW_ADMENU_MESSAGE, '', '', 'information');
    } else {
        adminmenu(1);
    }


// build todo list
$todo = array();
$todocnt = 0;

$op='';
if (isset($fixmp_status)) {
    unset($fixmp_status);
}
if (isset($_GET['op'])) {
    $op = cleaner($_GET['op']);
}
if ($op=='fixmp') {
    // try and make the upload directory
    $pathname=getMediaUploadPath();
    $mode=0700;
    $recursive=true;
    $fixmp_status=mkdir($pathname, $mode, $recursive);
}

    // check mysql version
    $mysqlversion_required='4.1.0';

    $sql="select version() as version";
    $result = $xoopsDB->queryF($sql);
    if ($result) {
        while ($myrow=$xoopsDB->fetchArray($result)) {
            $mysqlversion=$myrow['version'];
        }
        if (version_compare($mysqlversion, $mysqlversion_required) < 0) {
            $message=sprintf(_MI_GWLOTO_AD_TODO_MYSQL, $mysqlversion_required, $mysqlversion);
            if ($xoop25plus) {
                $moduleAdmin->addConfigBoxLine('<span style="color:orange"><img src="../images/admin/warn.png" alt="!" />'.$message.'</span>', 'default');
            } else {
                ++$todocnt;
                $todo[$todocnt]['link']='index.php';
                $todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_RETRY;
                $todo[$todocnt]['msg']= $message;
            }
        }
    }

// check for InnoDB support in mysql. We should have bombed out in install, but ...

    $have_innodb=false;

    $sql="show ENGINES";
    $result = $xoopsDB->queryF($sql);
    if ($result) {
        while ($myrow=$xoopsDB->fetchArray($result)) {
            if ($myrow['Engine']=='InnoDB' && ($myrow['Support']=='YES' || $myrow['Support']=='DEFAULT')) {
                $have_innodb=true;
            }
        }
    }
    if (!$have_innodb) {
        $message=_AD_GWLOTO_AD_TODO_INNODB;
        if ($xoop25plus) {
            $moduleAdmin->addConfigBoxLine('<span style="color:orange"><img src="../images/admin/warn.png" alt="!" />'.$message.'</span>', 'default');
        } else {
            ++$todocnt;
            $todo[$todocnt]['link']='index.php';
            $todo[$todocnt]['linktext']=_AD_GWREPORTS_AD_TODO_RETRY;
            $todo[$todocnt]['msg']= $message;
        }
    }

// check for tcpdf
    $tcpdf_path='';
    $tcpdf_path=$xoopsModuleConfig['tcpdf_path'];
    if ($tcpdf_path!='' && !file_exists($tcpdf_path)) {
        $message=_MI_GWLOTO_AD_TODO_TCPDF_NOTFND;
        if ($xoop25plus) {
            $moduleAdmin->addConfigBoxLine('<span style="color:orange"><img src="../images/admin/warn.png" alt="!" />'.$message.'</span>', 'default');
        } else {
            ++$todocnt;
            $todo[$todocnt]['link']=XOOPS_URL . "/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid');
            $todo[$todocnt]['linktext']=_MI_GWLOTO_ADMENU_PREF;
            $todo[$todocnt]['msg']= $message;
        }
    } else {
        if ($tcpdf_path=='') {
            if (!file_exists('../tcpdf/tcpdf.php') && !file_exists(XOOPS_ROOT_PATH.'/libraries/tcpdf/tcpdf.php')) {
                $message=_MI_GWLOTO_AD_TODO_TCPDF_INSTALL;// .sprintf(_MI_GWLOTO_AD_TODO_TCPDF_GENERAL,XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/tcpdf/');
                if ($xoop25plus) {
                    $moduleAdmin->addConfigBoxLine('<span style="color:orange"><img src="../images/admin/warn.png" alt="!" />'.$message.'</span>', 'default');
                } else {
                    ++$todocnt;
                    $todo[$todocnt]['link']='index.php';
                    $todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_RETRY;
                    $todo[$todocnt]['msg']= $message;
                }
            } else {
                if (!file_exists('../tcpdf/tcpdf.php')) {
                    $message=_MI_GWLOTO_AD_TODO_TCPDF_UPGRADE; // .sprintf(_MI_GWLOTO_AD_TODO_TCPDF_GENERAL,XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/tcpdf/');
                    if ($xoop25plus) {
                        $moduleAdmin->addConfigBoxLine('<span style="color:orange"><img src="../images/admin/warn.png" alt="!" />'.$message.'</span>', 'default');
                    } else {
                        ++$todocnt;
                        $todo[$todocnt]['link']='index.php';
                        $todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_RETRY;
                        $todo[$todocnt]['msg']= $message;
                    }
                }
            }
        }
    }

// check for a top level place
$sql="SELECT count(*) as rowcount FROM ".$xoopsDB->prefix('gwloto_place')." WHERE parent_id=0";
$result = $xoopsDB->query($sql);
$cnt=0;
if ($result) {
    $myrow=$xoopsDB->fetchArray($result);
    $cnt=$myrow['rowcount'];
}
if ($cnt==0) {
    $message = _MI_GWLOTO_AD_TODO_PLACES;
    if ($xoop25plus) {
        $moduleAdmin->addConfigBoxLine('<span style="color:orange"><img src="../images/admin/warn.png" alt="!" />'.$message.'</span>', 'default');
    } else {
        ++$todocnt;
        $todo[$todocnt]['link']='addplace.php';
        $todo[$todocnt]['linktext']=_MI_GWLOTO_AD_PLACE_EDIT_BUTTON;
        $todo[$todocnt]['msg']=$message;
    }
}

// check media upload directory permissions
$pathname=getMediaUploadPath();
if ($xoop25plus) {
    $moduleAdmin->addConfigBoxLine($pathname, 'folder');
} else {
    if (!is_writable($pathname)) {
        ++$todocnt;
        $todo[$todocnt]['link']='index.php?op=fixmp';
        if (isset($fixmp_status)) {
            $todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_FIX_FAILED;
        } else {
            $todo[$todocnt]['linktext']=_MI_GWLOTO_AD_TODO_FIX;
        }
        $todo[$todocnt]['msg']= sprintf(_MI_GWLOTO_AD_TODO_UPLOAD, $pathname);
    }
}

// display todo list
if ($xoop25plus) {
    echo $moduleAdmin->renderIndex();
}
if ($todocnt>0 && !$xoop25plus) {
    $teven='class="even"';
    $todd='class="odd"';
    $tclass=$teven;
    echo '<table width="100%" border="1" cellspacing="1" class="outer">';
    echo  '<tr><th colspan="2">'._MI_GWLOTO_AD_TODO_TITLE.'</th></tr>';
    echo '<tr><th width="25%">'._MI_GWLOTO_AD_TODO_ACTION.'</th><th>'._MI_GWLOTO_AD_TODO_MESSAGE.'</th></tr>';

    for ($i=1; $i<=$todocnt; ++$i) {
        if ($tclass==$todd) {
            $tclass=$teven;
        } else {
            $tclass=$todd;
        }
        echo '<tr cellspacing="2" cellpadding="2" '.$tclass.'>';
        echo '<td><a href="'.$todo[$i]['link'].'">'.$todo[$i]['linktext'].'</a></td>';
        echo '<td>'.$todo[$i]['msg'].'</a></td>';
        echo '</tr>';
    }
    echo '</table>';
}

// about section
if (!$xoop25plus) {
    echo'<table width="100%" border="0" cellspacing="1" class="outer">';
    echo '<tr><th>'._MI_GWLOTO_ADMENU_ABOUT.'</th></tr><tr><td width="100%" >';
    echo '<center><br /><b>'. _MI_GWLOTO_DESC . '</b></center><br />';
    echo '<center>Brought to you by <a href="http://www.geekwright.com/" target="_blank">geekwright, LLC</a></center><br />';
    echo '</td></tr>';
    echo '</table>';
}

include 'footer.php';
