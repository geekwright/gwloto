<?php
/**
* menu.php - define adminmenu
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

$adminmenu[1]['title'] = _MI_GWLOTO_ADMENU;
$adminmenu[1]['link'] = "admin/index.php";
$adminmenu[1]['icon'] = "images/icon_home.png";

$adminmenu[2]['title'] = _MI_GWLOTO_ADMENU_PLACE;
$adminmenu[2]['link'] = "admin/addplace.php";
$adminmenu[2]['icon'] = "images/icon_places.png";

$adminmenu[3]['title'] = _MI_GWLOTO_ADMENU_LANG;
$adminmenu[3]['link'] = "admin/language.php";
$adminmenu[3]['icon'] = "images/icon_languages.png";

$adminmenu[4]['title'] = _MI_GWLOTO_ADMENU_PLUGINS;
$adminmenu[4]['link'] = "admin/plugins.php";
$adminmenu[4]['icon'] = "images/icon_plugins.png";

?>