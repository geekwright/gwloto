<?php
/**
* install.php - initializations on module installation
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

include_once(dirname(__FILE__).'/pluginfunctions.php');

function xoops_module_install_gwloto(&$module)
{
    global $xoopsDB,$xoopsConfig;

    // add default language
    $folder=$xoopsConfig['language'];
    $sql ="INSERT INTO ".$xoopsDB->prefix('gwloto_language');
    $sql.=" (language_id, language, language_code, language_folder) VALUES (0, 'default', '', '$folder')";
    $result = $xoopsDB->queryF($sql);

    // add basic set of print plugins
    $pluginfiles=array(
        'joblog.php',
        'tagdemo.php',
        'sixuptag.php'
    );

    $path = dirname(dirname(__FILE__)) .'/plugins/';
    foreach ($pluginfiles as $filename) {
        installPluginFromFile($path.$filename);
    }

    $module->setErrors("Install Post-Process Completed");
    return true;
}
