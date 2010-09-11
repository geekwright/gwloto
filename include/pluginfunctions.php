<?php
/**
* pluginfunctions.php - common functions for plugin handling
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
include_once (dirname( __FILE__ ).'/dbcommon.php');

function getInstalledPlugins($lid=0) {

	global $xoopsDB;
	$installed_plugins=array();

	$sql='SELECT * FROM '. $xoopsDB->prefix('gwloto_plugin_register').', '.$xoopsDB->prefix('gwloto_plugin_name');
	$sql.= " WHERE (language_id=$lid OR language_id=0) ";
	$sql.= ' AND plugin = plugin_id ';
	$sql.= ' ORDER BY plugin_id, language_id ';
	
	$result = $xoopsDB->query($sql);
	if ($result) {
		while($myrow=$xoopsDB->fetchArray($result)) {
			$installed_plugins[$myrow['plugin_filename']]=array(
				'plugin_id' => $myrow['plugin_id'],
				'type' => $myrow['plugin_type'],
				'link' => $myrow['plugin_link'],
				'language_file' => $myrow['plugin_language_filename'],
				'name' => $myrow['plugin_name'],
				'description'=>$myrow['plugin_description']);
		}
	}
	
	return $installed_plugins;
}

function readPluginFromFile($filename) {

	$rawplugin=file_get_contents($filename);
	$tag='plugin';
	$start=stripos ($rawplugin , '['.$tag.']', 0);
	if(!$start) return false;

	$stop=stripos ($rawplugin , '[/'.$tag.']', $start);
	if(!$stop) return false;

	$rawplugin=substr($rawplugin,$start,$stop-$start);

	$tags=array(
		'type',
		'link',
//		'filename',
		'language_file',
		'name',
		'description');

	$plugin=array();
	$plugin['filename']=basename($filename);
	foreach($tags as $tag) {
		$plugin[$tag]='';
		$start=stripos ($rawplugin , '['.$tag.']', 0);
		if($start) {
			$start=$start+strlen($tag)+2;
			$stop=stripos ($rawplugin , '[/'.$tag.']', $start);
			if($stop) {
				$plugin[$tag]=substr($rawplugin,$start,$stop-$start);
			}
		}
	}
	return $plugin;
}

function installPluginFromFile($filename) {
	global $xoopsDB, $xoopsUser, $message, $errmessage;

	$myuserid=0;
	if($xoopsUser) {
		$myuserid = $xoopsUser->getVar('uid');
	}

	$plugin_type='';
	$plugin_seq=0;
	$plugin_link='';
	$plugin_filename='';
	$plugin_language_filename='';
	$plugin_name='';
	$plugin_description='';

	$plugin=readPluginFromFile($filename);

	if($plugin) {
		foreach($plugin as $i=>$v) {
			switch($i) {
				case 'type':
					$plugin_type=$v;
					break;
				case 'link':
					$plugin_link=$v;
					break;
				case 'filename':
					$plugin_filename=$v;
					break;
				case 'language_file':
					$plugin_language_filename=$v;
					break;
				case 'name':
					$plugin_name=$v;
					break;
				case 'description':
					$plugin_description=$v;
					break;
			}
		}

		$myts = myTextSanitizer::getInstance();

		$sl_plugin_type=$myts->addslashes($plugin_type);
		$sl_plugin_link=$myts->addslashes($plugin_link);
		$sl_plugin_filename=$myts->addslashes($plugin_filename);
		$sl_plugin_language_filename=$myts->addslashes($plugin_language_filename);
		$sl_plugin_name=$myts->addslashes($plugin_name);
		$sl_plugin_description=$myts->addslashes($plugin_description);

		$dberr=false;
		$dbmsg='';
		startTransaction();

		$sql='SELECT max(plugin_seq) as highseq FROM '. $xoopsDB->prefix('gwloto_plugin_register');
		$sql.= " WHERE plugin_type = '$sl_plugin_type' ";
		
		$result = $xoopsDB->query($sql);
		if ($result) {
			$myrow=$xoopsDB->fetchArray($result);
			if($myrow) {
				$plugin_seq=$myrow['highseq'];
			}
		}
		++$plugin_seq;

		$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_plugin_register');
		$sql.=' (plugin_type, plugin_seq, plugin_link, plugin_filename, plugin_language_filename, last_changed_by, last_changed_on) ';
		$sql.=" VALUES ('$sl_plugin_type', $plugin_seq, '$sl_plugin_link', '$sl_plugin_filename', '$sl_plugin_language_filename', $myuserid, UNIX_TIMESTAMP() )";
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}

		if(!$dberr) {
			$new_plugin_id = $xoopsDB->getInsertId();
			$sql ='INSERT INTO '.$xoopsDB->prefix('gwloto_plugin_name');
			$sql.=' (plugin, plugin_name, plugin_description) ';
			$sql.=" VALUES ($new_plugin_id, '$sl_plugin_name', '$sl_plugin_description' )";

			$result = $xoopsDB->queryF($sql);
			if (!$result) {
				$dberr=true;
				$dbmsg=formatDBError();
			}
		}

		if(!$dberr) {
			commitTransaction();
			$message = _MI_GWLOTO_AD_PLUGINS_ADD_OK;
		}
		else {
			rollbackTransaction();
			$err_message = _MI_GWLOTO_AD_PLUGINS_ADD_ERR .' '.$dbmsg;
		}
	}

	else $err_message = _MI_GWLOTO_AD_PLUGINS_INVALID;
}

function uninstallPlugin($plugin_id) {
	global $xoopsDB, $xoopsUser, $message, $errmessage;

	$dberr=false;
	$dbmsg='';
	startTransaction();

	$sql ='DELETE FROM '.$xoopsDB->prefix('gwloto_plugin_name');
	$sql.=" WHERE plugin = $plugin_id ";

	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$dberr=true;
		$dbmsg=formatDBError();
	}

	if(!$dberr) {
		$sql ='DELETE FROM '.$xoopsDB->prefix('gwloto_plugin_register');
		$sql.=" WHERE plugin_id = $plugin_id ";

		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$dberr=true;
			$dbmsg=formatDBError();
		}
	}

	if(!$dberr) {
		commitTransaction();
		$message = _MI_GWLOTO_AD_PLUGINS_DEL_OK;
	}
	else {
		rollbackTransaction();
		$err_message = _MI_GWLOTO_AD_PLUGINS_DEL_ERR .' '.$dbmsg;
	}

}

?>