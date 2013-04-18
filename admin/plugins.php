<?php
/**
* plugins.php - install or remove plugins
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
include_once ('../include/pluginfunctions.php');

if($xoop25plus) {
	echo $moduleAdmin->addNavigation('plugins.php');
}
else { // !$xoop25plus
	adminmenu(5);
}

function getLanguageByFolder() {
	global $xoopsDB;
	$langs=array();

	$sql='SELECT language_id, language_folder FROM '.$xoopsDB->prefix('gwloto_language');
	$sql.=' ORDER BY language_folder ';

	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$langs[$myrow['language_folder']] = $myrow['language_id'];
		}
	}
	return $langs;
}

// handle actions
//echo '<pre>$_POST='.print_r($_POST,true).'</pre>';

$language=0;
if(isset($_SESSION['UserLanguage'])) {
	$langfolders=getLanguageByFolder();
	if(isset($langfolders[$_SESSION['UserLanguage']])) $language=$langfolders[$_SESSION['UserLanguage']];
}

$op='display';
if(isset($_POST['op'])) $op=$_POST['op'];
if($op=='modify') {
	if(isset($_POST['delete'])) $op='del';
	if(isset($_POST['edit'])) $op='edit';
}

if($op=='edit') {
	if(isset($_POST['plugin_id'])) {
		$plugin_id=intval($_POST['plugin_id']);
		$dirname=$xoopsModule->getInfo('dirname');
		header('Location: ' . XOOPS_URL . '/modules/'.$dirname.'/editplugin.php?plugin_id='.$plugin_id);
		exit;
	}
	else $op='display';
}

if($op=='del') {
	if(isset($_POST['plugin_id'])) $plugin_id=intval($_POST['plugin_id']);
	else $op='display';
}

if($op=='del') {
	uninstallPlugin($plugin_id);
}

if($op=='add') {
	if(isset($_POST['filename'])) $plugin_file=$_POST['filename'];
	else $op='display';
}

if($op=='add') {
	installPluginFromFile('../plugins/'.$plugin_file);
}

if(isset($err_message)) echo '<strong>'.$err_message.'</strong>';
if(isset($message)) echo '<em>'.$message.'</em>';

// build plugin list
$installed_plugins=getInstalledPlugins($language);
$available_plugins=array();
$pluginfiles=glob('../plugins/*.php');

foreach($pluginfiles as $filename) {
	$plugin=readPluginFromFile($filename);
	if($plugin) $available_plugins[]=$plugin;
}

echo "<table width='100%' border='0' cellspacing='2' cellpadding='2' class='outer'>";
echo '<tr><th colspan="6" align="center">'._MI_GWLOTO_AD_PLUGINS_FORMNAME.'</th></tr>';
echo '<tr>';
echo '<th>'._MI_GWLOTO_AD_PLUGINS_TYPE.'</th>';
echo '<th>'._MI_GWLOTO_AD_PLUGINS_FILE.'</th>';
echo '<th>'._MI_GWLOTO_AD_PLUGINS_NAME.'</th>';
echo '<th>'._MI_GWLOTO_AD_PLUGINS_DESC.'</th>';
echo '<th>'._MI_GWLOTO_AD_PLUGINS_STATUS.'</th>';
echo '<th>'._MI_GWLOTO_AD_PLUGINS_ACTION.'</th>';
echo '</tr>';

$cnt=0;
$teven='class="even"';
$todd='class="odd"';
$tclass=$teven;
foreach($available_plugins as $plugin) {
	++$cnt;
	if($tclass==$todd) $tclass=$teven;
	else $tclass=$todd;
	echo '<tr cellspacing="2" cellpadding="2" '.$tclass.'>';

	if(isset($installed_plugins[$plugin['filename']])) {
		echo '<td>'.$plugin['type'].'</td>';
		echo '<td>'.$plugin['filename'].'</td>';
		echo '<td>'.$installed_plugins[$plugin['filename']]['name'].'</td>';
		echo '<td>'.$installed_plugins[$plugin['filename']]['description'].'</td>';

		echo '<td>'._MI_GWLOTO_AD_PLUGINS_INSTALLED.'</td>';
		$form='<form name="pluginaction" action="plugins.php" method="post" />';
		$form.='<input type="hidden" name="plugin_id" value="'.$installed_plugins[$plugin['filename']]['plugin_id'].'" />';
		$form.='<input type="hidden" name="op" value="modify" />';
		$form.='<input type="submit" name="delete" value="'._MI_GWLOTO_AD_PLUGINS_ACTION_DEL.'" onClick="return confirm(\''._MI_GWLOTO_AD_PLUGINS_DEL_CONFIRM.'\')">';
		$form.='<input type="submit" name="edit" value="'._MI_GWLOTO_AD_PLUGINS_ACTION_EDIT.'" />';
		$form.='</form>';
		echo '<td>'.$form.'</td>';
	}
	else {
		echo '<td>'.$plugin['type'].'</td>';
		echo '<td>'.$plugin['filename'].'</td>';
		echo '<td>'.$plugin['name'].'</td>';
		echo '<td>'.$plugin['description'].'</td>';

		echo '<td>'._MI_GWLOTO_AD_PLUGINS_NOTINSTALLED.'</td>';
		$form='<form name="pluginaction" action="plugins.php" method="post">';
		$form.='<input type="hidden" name="filename" value="'.$plugin['filename'].'" />';
		$form.='<input type="hidden" name="op" value="add" />';
		$form.='<input type="submit" value="'._MI_GWLOTO_AD_PLUGINS_ACTION_ADD.'" />';
		$form.='</form>';
		echo '<td>'.$form.'</td>';
	}
	echo '</tr>';
}
if($cnt==0) echo '<tr><td colspan="6" align="center">'._MI_GWLOTO_AD_PLUGINS_LISTEMPTY.'</td></tr>';
echo "</table>";
$dirname=$xoopsModule->getInfo('dirname');
echo '<br /><a href="'.XOOPS_URL.'/modules/'.$dirname.'/sortplugins.php">'._MI_GWLOTO_AD_PLUGINS_ACTION_SORT.'</a>';

include 'footer.php';
?>
