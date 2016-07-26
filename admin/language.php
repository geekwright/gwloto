<?php
/**
* language.php - add and edit languages used in gwloto
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
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

if ($xoop25plus) {
    echo $moduleAdmin->addNavigation('language.php');
} else { // !$xoop25plus
    adminmenu(4);
}
    $myuserid = $xoopsUser->getVar('uid');

    $admin_uid=$myuserid;
    if (isset($_POST['admin_uid'])) {
        $op = intval($_POST['admin_uid']);
    }
    $language_name='';
    if (isset($_POST['language_name'])) {
        $language_name = cleaner($_POST['language_name']);
    }
    $language_code='';
    if (isset($_POST['language_code'])) {
        $language_code = cleaner($_POST['language_code']);
    }
    $language_folder='';
    if (isset($_POST['language_folder'])) {
        $language_folder = cleaner($_POST['language_folder']);
    }
    $language_id='';
    if (isset($_POST['language_id'])) {
        $language_id = intval(cleaner($_POST['language_id']));
    }
    $op='display';
    if (isset($_POST['op'])) {
        $op = cleaner($_POST['op']);
    }

    if ($op != 'update' && $op != 'edit' && $op != 'add') {
        $op='display';
    }

    if ($op=='edit') {
        // get language to edit
        $sql="SELECT * FROM ".$xoopsDB->prefix('gwloto_language');
        $sql.=" WHERE language_id = $language_id ";
        $result = $xoopsDB->query($sql);
        if ($result) {
            $myrow=$xoopsDB->fetchArray($result);
            $language_name=$myrow['language'];
            $language_code=$myrow['language_code'];
            $language_folder=$myrow['language_folder'];
        }
    }

    if ($language_name=='') {
        $op='display';
    }

    if ($op=='add') {
        // get top used language_id
        $sql="SELECT max(language_id) as maxid FROM ".$xoopsDB->prefix('gwloto_language');
        $result = $xoopsDB->query($sql);
        if ($result) {
            $myrow=$xoopsDB->fetchArray($result);
            $maxlangid=$myrow['maxid'];
        }
        $language_id=$maxlangid+1;
        // insert new gwloto_language
        $sql ="INSERT INTO ".$xoopsDB->prefix('gwloto_language');
        $sql.=' (language_id, language, language_code, language_folder)';
        $sql.=" VALUES ($language_id, '$language_name', '$language_code', '$language_folder')";
        $result = $xoopsDB->queryF($sql);
        if ($result) {
            $message = _MI_GWLOTO_AD_LANG_ADD_OK;
            redirect_header('language.php', 3, $message);
        } else {
            $dbmsg=formatDBError();
            $err_message = _MI_GWLOTO_AD_LANG_ADD_ERR .' '.$dbmsg;
        }
    }

    if ($op=='update') {
        // update existing gwloto_language
        $sql ="UPDATE ".$xoopsDB->prefix('gwloto_language');
        $sql.=" SET language = '$language_name' ";
        $sql.=", language_code = '$language_code' ";
        $sql.=", language_folder = '$language_folder' ";
        $sql.=" WHERE language_id=$language_id ";
        $result = $xoopsDB->queryF($sql);
        if ($result) {
            $message = _MI_GWLOTO_AD_LANG_EDIT_OK;
            redirect_header('language.php', 3, $message);
        } else {
            $dbmsg=formatDBError();
            $err_message = _MI_GWLOTO_AD_LANG_EDIT_ERR .' '.$dbmsg;
        }
    }

    if (isset($err_message)) {
        echo "<br><br><b>$err_message</b><br><br>";
    }

    $token=1;

    if ($op=='edit') {
        $form = new XoopsThemeForm(_MI_GWLOTO_AD_LANG_FORMNAME_EDIT, 'form1', 'language.php', 'POST', $token);
        $form->addElement(new XoopsFormHidden('op', 'update'));
        $form->addElement(new XoopsFormHidden('language_id', $language_id));
    } else {
        $form = new XoopsThemeForm(_MI_GWLOTO_AD_LANG_FORMNAME, 'form1', 'language.php', 'POST', $token);
        $form->addElement(new XoopsFormHidden('op', 'add'));
    }

    $caption = _MI_GWLOTO_AD_LANG_NAME;
    $form->addElement(new XoopsFormText($caption, 'language_name', 50, 100, htmlspecialchars($language_name, ENT_QUOTES)), true);

    $caption = _MI_GWLOTO_AD_LANG_CODE;
    $form->addElement(new XoopsFormText($caption, 'language_code', 5, 20, htmlspecialchars($language_code, ENT_QUOTES)), true);

    $caption = _MI_GWLOTO_AD_LANG_FOLDER;
    $form->addElement(new XoopsFormText($caption, 'language_folder', 50, 100, htmlspecialchars($language_folder, ENT_QUOTES)), true);

    if ($op=='edit') {
        $form->addElement(new XoopsFormButton(_MI_GWLOTO_AD_LANG_UPDATE_CAPTION, 'submit', _MI_GWLOTO_AD_LANG_UPDATE_BUTTON, 'submit'));
    } else {
        $form->addElement(new XoopsFormButton(_MI_GWLOTO_AD_LANG_ADD_CAPTION, 'submit', _MI_GWLOTO_AD_LANG_ADD_BUTTON, 'submit'));
    }

    //$form->display();
    $body=$form->render();

echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td width='100%' >";
echo $body;
echo '</td></tr>';
echo "</table>";

echo "<table width='100%' border='0' cellspacing='1' class='outer'>";
echo '<tr><th colspan="5" align="center">'._MI_GWLOTO_AD_LANG_LISTNAME.'</th></tr>';
echo '<tr><th>'._MI_GWLOTO_AD_LANG_ID.'</th><th>'._MI_GWLOTO_AD_LANG_NAME.'</th>';
echo '<th>'._MI_GWLOTO_AD_LANG_CODE.'</th><th>'._MI_GWLOTO_AD_LANG_FOLDER.'</th>';
echo "<th>"._MI_GWLOTO_AD_LANG_EDIT_BUTTON."</tr>";


$sql="SELECT * FROM ".$xoopsDB->prefix('gwloto_language');
$sql.=" ORDER BY language_id";

$result = $xoopsDB->query($sql);
$cnt=0;
$teven='class="even"';
$todd='class="odd"';
$tclass=$teven;
if ($result) {
    while ($myrow=$xoopsDB->fetchArray($result)) {
        ++$cnt;
        if ($tclass==$todd) {
            $tclass=$teven;
        } else {
            $tclass=$todd;
        }
        echo '<tr cellspacing="2" cellpadding="2" '.$tclass.'>';
        echo '<td>'.$myrow['language_id'].'</td><td>'.$myrow['language'].'</td>';
        echo '<td>'.$myrow['language_code'].'</td><td>'.$myrow['language_folder'].'</td>';

        $form="<form action='language.php' method='post'>";
        $form.="<input type='hidden' name='op' value='edit' />";
        $form.="<input type='hidden' name='language_id' value='".$myrow['language_id']."' />";
        $form.="<input type='submit' class='formButton' name='submit' value='"._MI_GWLOTO_AD_LANG_EDIT_BUTTON."' />";
        $form.="</form>";

        echo "<td>$form</td></tr>";
    }
}
if ($cnt==0) {
    echo '<tr><td colspan="3" align="center">'._MI_GWLOTO_AD_LANG_LISTEMPTY.'</td></tr>';
}
echo "</table>";

include 'footer.php';
