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

if(file_exists(XOOPS_ROOT_PATH.'/Frameworks/moduleclasses/icons/32/about.png')) {
$pathIcon32='../../Frameworks/moduleclasses/icons/32';

$adminmenu[1] = array(
	'title' => _MI_GWLOTO_ADMENU,
	'link'  => 'admin/index.php',
	'icon'  => $pathIcon32.'/home.png'
);

$adminmenu[] = array(
	'title' => _MI_GWLOTO_ADMENU_ABOUT,
	'link'  => 'admin/about.php',
	'icon'  => $pathIcon32.'/about.png'
);

$adminmenu[] = array(
	'title' => _MI_GWLOTO_ADMENU_PLACE,
	'link'  => 'admin/addplace.php',
	'icon'  => 'images/admin/places.png'
);

$adminmenu[] = array(
	'title' => _MI_GWLOTO_ADMENU_LANG,
	'link'  => 'admin/language.php',
	'icon'  => 'images/admin/languages.png'
);

$adminmenu[] = array(
	'title' => _MI_GWLOTO_ADMENU_PLUGINS,
	'link'  => 'admin/plugins.php',
	'icon'  => 'images/admin/plugins.png'
);

} else {

$adminmenu[1] = array(
	'title' => _MI_GWLOTO_ADMENU,
	'link'  => 'admin/index.php',
	'icon'  => 'images/admin/home.png'
);

$adminmenu[] = array(
	'title' => _MI_GWLOTO_ADMENU_ABOUT,
	'link'  => 'admin/about.php',
	'icon'  => 'images/admin/about.png'
);

$adminmenu[] = array(
	'title' => _MI_GWLOTO_ADMENU_PLACE,
	'link'  => 'admin/addplace.php',
	'icon'  => 'images/admin/places.png'
);

$adminmenu[] = array(
	'title' => _MI_GWLOTO_ADMENU_LANG,
	'link'  => 'admin/language.php',
	'icon'  => 'images/admin/languages.png'
);

$adminmenu[] = array(
	'title' => _MI_GWLOTO_ADMENU_PLUGINS,
	'link'  => 'admin/plugins.php',
	'icon'  => 'images/admin/plugins.png'
);

}
?>
