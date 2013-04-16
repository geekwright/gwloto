<?php
/**
* update.php - tweaks on module update
*
* This file is part of gwloto - geekwright lockout tagout
*
* @copyright  Copyright © 2010-2011 geekwright, LLC. All rights reserved. 
* @license    gwloto/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwloto
* @version    $Id$
*/

if (!defined("XOOPS_ROOT_PATH")) die("XOOPS root path not defined");

function xoops_module_update_gwloto(&$module, $old_version) {
	global $xoopsDB;
	$ourdir=$module->getVar('dirname');
    if ($old_version < 110) //If upgrade from gwloto older than 1.10
	{
	    //$xoopsDB->queryFromFile(XOOPS_ROOT_PATH.'/modules/'.$ourdir.'/sql/upgrade-1.0-to-1.1.sql');
    	$sql="CREATE TABLE ".$xoopsDB->prefix('gwloto_group_auth'). " (" .
			"  groupid int(8) unsigned NOT NULL," .
			"  place_id int(8) unsigned NOT NULL," .
			"  authority int(8) unsigned NOT NULL default '0'," .
			"  last_changed_by int(8) NOT NULL," .
			"  last_changed_on int(10) NOT NULL," .
			"  PRIMARY KEY (groupid, place_id, authority)," .
			"  KEY (authority,place_id)" .
			" ) ENGINE=InnoDB DEFAULT CHARSET=utf8; "; 
		$xoopsDB->queryF($sql);
    }

//    $module->setErrors("Update Post-Process Completed");
    return true;
}


?>
