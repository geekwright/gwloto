<?php
/**
* functions.php - admin area functions
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
function loadmodinfo($langdir)
{
global $xoopsModule;
	if (file_exists(XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/language/'.$langdir.'/modinfo.php')) {
        include_once XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/language/'.$langdir.'/modinfo.php';
		return true;
    }
	return false;
}
function adminmenu($currentoption=0, $breadcrumb = "")
{
    global $xoopsModule, $xoopsConfig;
    $tblColors=Array();
    $tblColors[0]=$tblColors[1]=$tblColors[2]=$tblColors[3]=$tblColors[4]=$tblColors[5]=$tblColors[6]=$tblColors[7]=$tblColors[8]='';
    if($currentoption>=0) {
    $tblColors[$currentoption]='id=\'current\'';;
	}
	if(isset($_SESSION['UserLanguage'])) {
		if(loadmodinfo($_SESSION['UserLanguage'])==false) {
			if(loadmodinfo($xoopsConfig['language'])==false) {
				loadmodinfo('english');
			}
		}
	}

    /* Nice buttons styles */
    $return = "
    	<style type='text/css'>
    	
    	#buttontop { float:left; width:100%; background: #dae0d2; font-size:93%; line-height:normal; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; margin: 0; }
    			
		#admintabs {
        	FONT-SIZE: 93%; BACKGROUND: url(../images/bg.gif) #dae0d2 repeat-x 50% bottom; FLOAT: left; WIDTH: 100%; LINE-HEIGHT: normal; border-left: 1px solid black; border-right: 1px solid black;
        }
        #admintabs ul {
        	PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 0px; MARGIN: 0px; PADDING-TOP: 10px; LIST-STYLE-TYPE: none;
        }
        #admintabs li {
        	PADDING-RIGHT: 0px; PADDING-LEFT: 9px; BACKGROUND: url(../images/left.gif) no-repeat left top; FLOAT: left; PADDING-BOTTOM: 0px; MARGIN: 0px; PADDING-TOP: 0px; list-style: none;
        }
        #admintabs A {
        	PADDING-RIGHT: 15px; DISPLAY: block; PADDING-LEFT: 6px; FONT-WEIGHT: bold; BACKGROUND: url(../images/right.gif) no-repeat right top; FLOAT: left; PADDING-BOTTOM: 4px; COLOR: #765; PADDING-TOP: 5px; TEXT-DECORATION: none
        }
        #admintabs A {
        	FLOAT: left;
        }
        #admintabs A:hover {
        	COLOR: #333
        }
        #admintabs #current {
        	BACKGROUND-IMAGE: url(../images/left_on.gif)
        }
        #admintabs #current A {
        	BACKGROUND-IMAGE: url(../images/right_on.gif); COLOR: #333; float:left;
        }
		</style>
    ";
    
    include XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar('dirname')."/admin/menu.php";

    $return .= "<div id='buttontop'>";
    $return .= "<table style=\"width: 100%; padding: 0; \" cellspacing=\"0\"><tr>";
    $return .= "<td style='width: 60%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;'><a class='nobutton' href='" . XOOPS_URL . "/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid') . "'>" . _MI_GWLOTO_ADMENU_PREF . "</a> | <a href='" . XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/index.php'>" . _MI_GWLOTO_ADMENU_GOMOD . "</a></td>";
    $return .= "<td style='width: 40%; font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;'>&nbsp;" . $breadcrumb . "</td>";
    $return .= "</tr></table>";
    $return .= "</div>";

    $return .= "<div id='admintabs'>";
    $return .= "<ul>";
    foreach ($adminmenu as $key => $menu) {
        $return .= "<li ". $tblColors[$key] . "><a href=\"" . XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/".$menu['link']."\">" . $menu['title'] . "</a></li>";
    }
    $return .= "</ul></div><div style=\"clear:both;\"></div>";

    echo $return;

}

?>