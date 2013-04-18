<?php
/**
* dbcommon.php - database utility functions
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

function startTransaction() {
global $xoopsDB;
	$sql = 'START TRANSACTION';
	$result = $xoopsDB->queryF($sql);
	return $result;
}

function rollbackTransaction() {
global $xoopsDB;
	$sql = 'ROLLBACK';
	$result = $xoopsDB->queryF($sql);
	return $result;
}

function commitTransaction() {
global $xoopsDB;
	$sql = 'COMMIT';
	$result = $xoopsDB->queryF($sql);
	return $result;
}

function formatDBError() {
global $xoopsDB;

	$msg=$xoopsDB->errno() . ' ' . $xoopsDB->error();
	return($msg);
}

function cleaner($string) {
	if (get_magic_quotes_gpc()) $string = stripslashes($string); 
//	$string=stripcslashes($string);
	$string=html_entity_decode($string);
	$string=strip_tags($string);
	$string=trim($string);
	$string=stripslashes($string);
	return $string;
}

function dbescape($string) {
	return mysql_real_escape_string($string); 
}

function getMediaUploadPath() {
global $xoopsModuleConfig;

	$media_upload_path=$xoopsModuleConfig['media_upload_path'];
	return $media_upload_path;
}

function getTcpdfPath() {
global $xoopsModuleConfig;

	$tcpdf_path='';
	$tcpdf_path=$xoopsModuleConfig['tcpdf_path'];
	if($tcpdf_path=='') {
		if ( file_exists('tcpdf/tcpdf.php') ) {
			$tcpdf_path='tcpdf/tcpdf.php';
		} elseif ( file_exists(XOOPS_ROOT_PATH.'/libraries/tcpdf/tcpdf.php') ) {
			$tcpdf_path=XOOPS_ROOT_PATH.'/libraries/tcpdf/tcpdf.php';
		} else $tcpdf_path='';
	}
	return $tcpdf_path;
}

?>
