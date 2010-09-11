<?php
/**
* userauth.php - constants for gwloto_user_auth authority column
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

define('_GWLOTO_USERAUTH_PL_ADMIN', '1');	// set user authroities
define('_GWLOTO_USERAUTH_PL_EDIT',  '2');	// edit places
define('_GWLOTO_USERAUTH_PL_SUPER', '3');	// is supervisor
define('_GWLOTO_USERAUTH_PL_AUDIT', '4');	// view user authroities
define('_GWLOTO_USERAUTH_CP_EDIT',  '11');	// edit control plans
define('_GWLOTO_USERAUTH_CP_VIEW',  '12');	// view control plans
define('_GWLOTO_USERAUTH_JB_EDIT',  '21');	// edit jobs
define('_GWLOTO_USERAUTH_JB_VIEW',  '22');	// view jobs
define('_GWLOTO_USERAUTH_MD_EDIT',  '31');	// edit media
define('_GWLOTO_USERAUTH_MD_VIEW',  '32');	// view media
define('_GWLOTO_USERAUTH_PL_TRANS', '41');	// translate places
define('_GWLOTO_USERAUTH_CP_TRANS', '42');	// translate control plans
define('_GWLOTO_USERAUTH_MD_TRANS', '43');	// translate media

?>