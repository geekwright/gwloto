<?php
/**
* xoops_version.php - basic module definitions
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

if (!defined("XOOPS_ROOT_PATH")) die("Root path not defined");

$modversion['name'] = _MI_GWLOTO_NAME;
$modversion['version'] = '1.0';
$modversion['description'] = _MI_GWLOTO_DESC;
$modversion['author'] = "Richard Griffith - richard@geekwright.com";
$modversion['credits'] = "geekwight, LLC";
$modversion['license'] = 'GNU General Public License V2 or later';
$modversion['official'] = 0;
if (defined("ICMS_ROOT_PATH")) $modversion['image'] = "images/icon_big.png";
else $modversion['image'] = "images/icon.png";
$modversion['dirname'] = basename( dirname( __FILE__ ) ) ;

// things for ModuleAdmin() class
$modversion['license_url'] = XOOPS_URL.'/modules/gwreports/docs/license.txt';
$modversion['license_url'] = substr($modversion['license_url'],strpos($modversion['license_url'],'//')+2);
$modversion['release_date']     = '2013/05/01';
$modversion['module_website_url'] = 'geekwright.com';
$modversion['module_website_name'] = 'geekwright, LLC';
$modversion['module_status'] = "RC";
$modversion['min_php']='5.2';
$modversion['min_xoops']='2.5';
$modversion['system_menu'] = 1;
$modversion['help'] = "page=help";
// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Menu
$modversion['hasMain'] = 1;

// Search
$modversion['hasSearch'] = 0;

// comments
$modversion['hasComments'] = 0;
// notification
$modversion['hasNotification'] = 0;
// Config
$i=1;
$modversion['config'][$i]['name'] = 'maxtagcopies';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_MAXTAG';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_MAXTAG_DSC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '6';
++$i;
$modversion['config'][$i]['name'] = 'pref_date';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_PREF_DATE';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_PREF_DATE_DSC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'l F j, Y H:i:s';
++$i;
$modversion['config'][$i]['name'] = 'show_reconnect';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_SHOW_RECON';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_SHOW_RECON_DSC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
++$i;
$modversion['config'][$i]['name'] = 'show_inspect';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_SHOW_INSPECT';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_SHOW_INSPECT_DSC';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '1';
++$i;
$modversion['config'][$i]['name'] = 'jobrequires';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_JOB_REQUIRES';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_JOB_REQUIRES_DSC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'workorder,supervisor,startdate,enddate,description,stepname';
++$i;
$modversion['config'][$i]['name'] = 'planrequires';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_PLAN_REQUIRES';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_PLAN_REQUIRES_DSC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'review,hazard_inventory,required_ppe,authorized_personnel,additional_requirements,disconnect_instructions,reconnect_instructions,inspection_instructions,inspection_state';
++$i;
$modversion['config'][$i]['name'] = 'media_upload_path';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_MEDIA_PATH';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_MEDIA_PATH_DSC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = XOOPS_ROOT_PATH.'/uploads/'.$modversion['dirname'].'/media/';

++$i;
$modversion['config'][$i]['name'] = 'max_media_size';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_MAX_MEDIA_SIZE';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_MAX_MEDIA_SIZE_DSC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '5000000';

++$i;
$modversion['config'][$i]['name'] = 'enable_translate';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_ENABLE_TRANSLATE';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_ENABLE_TRANSLATE_DSC';
$modversion['config'][$i]['formtype'] = 'select';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '0';
$modversion['config'][$i]['options'] = array(_MI_GWLOTO_CFG_ENABLE_TRANSLATE_OFF=>0, _MI_GWLOTO_CFG_ENABLE_TRANSLATE_GOOGLE=>1, _MI_GWLOTO_CFG_ENABLE_TRANSLATE_BING=>2);

++$i;
$modversion['config'][$i]['name'] = 'translate_api_key';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_TRANSLATE_KEY';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_TRANSLATE_KEY_DSC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '';

++$i;
$modversion['config'][$i]['name'] = 'tcpdf_path';
$modversion['config'][$i]['title'] = '_MI_GWLOTO_CFG_TCPDF_PATH';
$modversion['config'][$i]['description'] = '_MI_GWLOTO_CFG_TCPDF_PATH_DSC';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '';

// Database
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['onInstall'] = 'include/install.php';
$modversion['onUpdate'] = 'include/update.php';
$modversion['onUninstall'] = 'include/uninstall.php';
$modversion['tables'][0] = 'gwloto_media';
$modversion['tables'][]  = 'gwloto_media_detail';
$modversion['tables'][]  = 'gwloto_media_file';
$modversion['tables'][]  = 'gwloto_media_attach';
$modversion['tables'][]  = 'gwloto_place';
$modversion['tables'][]  = 'gwloto_place_detail';
$modversion['tables'][]  = 'gwloto_language';
$modversion['tables'][]  = 'gwloto_user';
$modversion['tables'][]  = 'gwloto_user_auth';
$modversion['tables'][]  = 'gwloto_cplan';
$modversion['tables'][]  = 'gwloto_cplan_detail';
$modversion['tables'][]  = 'gwloto_cpoint';
$modversion['tables'][]  = 'gwloto_cpoint_detail';
$modversion['tables'][]  = 'gwloto_job';
$modversion['tables'][]  = 'gwloto_job_steps';
$modversion['tables'][]  = 'gwloto_job_places';
$modversion['tables'][]  = 'gwloto_plugin_register';
$modversion['tables'][]  = 'gwloto_plugin_name';
$modversion['tables'][]  = 'gwloto_group_auth';

// Blocks
$modversion['blocks'][1] = array(
  'file' => 'blocks.php',
  'name' => _MI_GWLOTO_ASSIGNED_BLOCK,
  'description' => _MI_GWLOTO_ASSIGNED_BLOCK_DESC,
  'show_func' => 'b_gwloto_assigned_block_show',
  'edit_func' => 'b_gwloto_assigned_block_edit',
  'options' => 'desc|10',
  'template' => 'gwloto_block.html');

// Templates
$i=1;
$modversion['templates'][$i]['file'] = 'gwloto_place_crumbs.html';
$modversion['templates'][$i]['description'] = 'Place Breadcrumbs';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_action_menu.html';
$modversion['templates'][$i]['description'] = 'Action Menu';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_header.html';
$modversion['templates'][$i]['description'] = 'Page Header';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_job_list.html';
$modversion['templates'][$i]['description'] = 'Job List';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_media_list.html';
$modversion['templates'][$i]['description'] = 'Attached Media';

++$i;
$modversion['templates'][$i]['file'] = 'gwloto_index.html';
$modversion['templates'][$i]['description'] = 'Module Index';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_editauths.html';
$modversion['templates'][$i]['description'] = 'Edit Authorities';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_viewplan.html';
$modversion['templates'][$i]['description'] = 'View Control Plan';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_editpoint.html';
$modversion['templates'][$i]['description'] = 'Edit Control Points';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_sortpoint.html';
$modversion['templates'][$i]['description'] = 'Sort Control Points';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_viewjob.html';
$modversion['templates'][$i]['description'] = 'View Job';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_printjob.html';
$modversion['templates'][$i]['description'] = 'Print Job';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_editmedia.html';
$modversion['templates'][$i]['description'] = 'Media Center';
++$i;
$modversion['templates'][$i]['file'] = 'gwloto_listjobs.html';
$modversion['templates'][$i]['description'] = 'List Jobs';
?>
