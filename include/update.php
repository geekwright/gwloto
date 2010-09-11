<?php
/**
* update.php - tweaks on module update
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

if (!defined("XOOPS_ROOT_PATH")) die("XOOPS root path not defined");

function xoops_module_update_gwloto(&$module, $old_version) {
// nothing to do yet
    $module->setErrors("Update Post-Process Completed");
    return true;
}


?>