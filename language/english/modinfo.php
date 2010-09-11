<?php
if (!defined('XOOPS_ROOT_PATH')) die('Root path not defined');
// Module Info

// The name and description of module
define('_MI_GWLOTO_NAME', 'gwloto');
define('_MI_GWLOTO_DESC', 'geekwright Lock Out Tag Out');

// Admin Menu
define('_MI_GWLOTO_ADMENU', 'gwloto Center');
define('_MI_GWLOTO_ADMENU_ABOUT', 'About');
define('_MI_GWLOTO_ADMENU_PLACE', 'Top Level Places');
define('_MI_GWLOTO_ADMENU_LANG', 'Languages');
define('_MI_GWLOTO_ADMENU_PLUGINS', 'Plugins');
define('_MI_GWLOTO_ADMENU_PREF', 'Preferences');
define('_MI_GWLOTO_ADMENU_GOMOD', 'Go to Module');

// Admin Root Places
define('_MI_GWLOTO_AD_PLACE_FORMNAME', 'Add a Top Level Place');
define('_MI_GWLOTO_AD_PLACE_ID', 'Place ID');
define('_MI_GWLOTO_AD_PLACE_NAME', 'Top Level Place Name');
define('_MI_GWLOTO_AD_PLACE_ADMIN', 'Admin for this Place');
define('_MI_GWLOTO_AD_PLACE_EDIT_BUTTON', 'Add Place');
define('_MI_GWLOTO_AD_PLACE_EDIT_CAPTION', 'Add Place with Administrator');
define('_MI_GWLOTO_AD_PLACE_LISTNAME', 'Defined Top Level Places');
define('_MI_GWLOTO_AD_PLACE_LISTEMPTY', 'No places defined');
define('_MI_GWLOTO_AD_PLACE_ADD_OK', 'Place added');
define('_MI_GWLOTO_AD_PLACE_ADD_ERR', 'Could not add place');

// Admin Plugins
define('_MI_GWLOTO_AD_PLUGINS_FORMNAME', 'Available Plugins');
define('_MI_GWLOTO_AD_PLUGINS_TYPE', 'Type');
define('_MI_GWLOTO_AD_PLUGINS_FILE', 'Filename');
define('_MI_GWLOTO_AD_PLUGINS_NAME', 'Name');
define('_MI_GWLOTO_AD_PLUGINS_DESC', 'Description');
define('_MI_GWLOTO_AD_PLUGINS_STATUS', 'Status');
define('_MI_GWLOTO_AD_PLUGINS_INSTALLED', 'Installed');
define('_MI_GWLOTO_AD_PLUGINS_NOTINSTALLED', '');
define('_MI_GWLOTO_AD_PLUGINS_ACTION', 'Action');
define('_MI_GWLOTO_AD_PLUGINS_ACTION_ADD', 'Install');
define('_MI_GWLOTO_AD_PLUGINS_ACTION_DEL', 'Remove');
define('_MI_GWLOTO_AD_PLUGINS_ACTION_EDIT', 'Edit');
define('_MI_GWLOTO_AD_PLUGINS_ACTION_SORT', 'Reorder Plugins');
define('_MI_GWLOTO_AD_PLUGINS_ADD_OK', 'Plugin added');
define('_MI_GWLOTO_AD_PLUGINS_ADD_ERR', 'Could not add plugin');
define('_MI_GWLOTO_AD_PLUGINS_DEL_OK', 'Plugin removed');
define('_MI_GWLOTO_AD_PLUGINS_DEL_ERR', 'Could not remove plugin');
define('_MI_GWLOTO_AD_PLUGINS_LISTEMPTY', 'No plugins found');
define('_MI_GWLOTO_AD_PLUGINS_DEL_CONFIRM', 'Uninstall this plugin?');
define('_MI_GWLOTO_AD_PLUGINS_INVALID', 'Failed to load file as plugin.');


// Admin Languages
define('_MI_GWLOTO_AD_LANG_FORMNAME', 'Add a Language');
define('_MI_GWLOTO_AD_LANG_ID', 'Language ID');
define('_MI_GWLOTO_AD_LANG_NAME', 'Language Caption');
define('_MI_GWLOTO_AD_LANG_CODE', 'ISO 639-1 Code');
define('_MI_GWLOTO_AD_LANG_FOLDER', 'Folder');
define('_MI_GWLOTO_AD_LANG_ADD_BUTTON', 'Add');
define('_MI_GWLOTO_AD_LANG_ADD_CAPTION', 'Add Language');
define('_MI_GWLOTO_AD_LANG_LISTNAME', 'Defined Languages');
define('_MI_GWLOTO_AD_LANG_LISTEMPTY', 'No languages defined');
define('_MI_GWLOTO_AD_LANG_ADD_OK', 'Language added');
define('_MI_GWLOTO_AD_LANG_ADD_ERR', 'Could not add language');
define('_MI_GWLOTO_AD_LANG_FORMNAME_EDIT', 'Update Language');
define('_MI_GWLOTO_AD_LANG_UPDATE_BUTTON', 'Update');
define('_MI_GWLOTO_AD_LANG_UPDATE_CAPTION', 'Update Language');
define('_MI_GWLOTO_AD_LANG_EDIT_BUTTON', 'Edit');
define('_MI_GWLOTO_AD_LANG_EDIT_OK', 'Language updated');
define('_MI_GWLOTO_AD_LANG_EDIT_ERR', 'Could not update language');

// todo list messages
define('_MI_GWLOTO_AD_TODO_TITLE', 'Action Required');
define('_MI_GWLOTO_AD_TODO_ACTION', 'Action');
define('_MI_GWLOTO_AD_TODO_MESSAGE', 'Message');
define('_MI_GWLOTO_AD_TODO_PLACES', 'You must add at least one top level place.');
define('_MI_GWLOTO_AD_TODO_MYSQL', 'MySQL version %1$s or above is required. (Detected=%2$s)');
define('_MI_GWLOTO_AD_TODO_UPGRADE', 'A newer version of '._MI_GWLOTO_NAME.' is available (Installed=%1$s Current=%2$s)');
define('_MI_GWLOTO_AD_TODO_UPLOAD', 'The upload path %1$s is not writable. Check that it exists and peremissions are correct.');
define('_MI_GWLOTO_AD_TODO_TCPDF_NOTFND', 'TCPDF was not found in the location specified in the module preferences. Please correct the preferences setting.');
define('_MI_GWLOTO_AD_TODO_TCPDF_INSTALL', 'TCPDF was not found. If it is installed and was not autodetected, please specify the location in the module preferences.');
define('_MI_GWLOTO_AD_TODO_TCPDF_UPGRADE', 'The TCPDF version located may be outdated, and may result in visble issues in the output of some plugins. Consider installing the current version for the best results.');
define('_MI_GWLOTO_AD_TODO_TCPDF_GENERAL', '<br /><br />TCPDF is a PHP class for generating PDF documents, and is is required for most supplied plugins to operate. For more information on TCPDF please see <a href="http://wwww.tcpdf.org/">www.tcpdf.org</a>.<br /><br />TCPDF will be automatically detected if installed in the  directory shown here:<br />%s');

define('_MI_GWLOTO_AD_TODO_RETRY', 'Retry');
define('_MI_GWLOTO_AD_TODO_FIX', 'Try to Fix');
define('_MI_GWLOTO_AD_TODO_FIX_FAILED', 'Could not fix');

// config options
define('_MI_GWLOTO_CFG_MAXTAG', 'Max Tag Copies');
define('_MI_GWLOTO_CFG_MAXTAG_DSC', 'Maximum number of Tag Copies and Locks Required');

define ('_MI_GWLOTO_CFG_PREF_DATE', "Date Format");
define ('_MI_GWLOTO_CFG_PREF_DATE_DSC', "Format passed to formatTimeStamp()");

define ('_MI_GWLOTO_CFG_SHOW_RECON', 'Use Reconnect');
define ('_MI_GWLOTO_CFG_SHOW_RECON_DSC', 'Show Reconnect instructions and sequences');
define ('_MI_GWLOTO_CFG_SHOW_INSPECT', 'Use Inspect');
define ('_MI_GWLOTO_CFG_SHOW_INSPECT_DSC', 'Show Inpsection instructions and sequences');

define('_MI_GWLOTO_CFG_JOB_REQUIRES','Fields Required on Job Entries');
define('_MI_GWLOTO_CFG_JOB_REQUIRES_DSC',"Comma separated list of fields required for New Job, Edit Job and Add Step entries. Possible values are: 'workorder', 'supervisor', 'startdate', 'enddate', 'description' and 'stepname'");

define('_MI_GWLOTO_CFG_PLAN_REQUIRES','Fields Required on Control Plan Entries');
define('_MI_GWLOTO_CFG_PLAN_REQUIRES_DSC',"Comma separated list of fields required for Control Plan and Control Points entries. Possible values are: 'review', 'hazard_inventory', 'required_ppe', 'authorized_personnel', 'additional_requirements', 'disconnect_instructions', 'reconnect_instructions', 'inspection_instructions' and 'inspection_state'");

define('_MI_GWLOTO_CFG_MEDIA_PATH','Media File Upload Path');
define('_MI_GWLOTO_CFG_MEDIA_PATH_DSC','Directory where media files are placed when uploaded to the server. Must be writable by the web server.');

define('_MI_GWLOTO_CFG_MAX_MEDIA_SIZE','Max Media File Size');
define('_MI_GWLOTO_CFG_MAX_MEDIA_SIZE_DSC','Maximum file size in bytes to allow in new media uploads.');

define('_MI_GWLOTO_CFG_ENABLE_GOOGLE_TRANSLATE','Enable Google Translation');
define('_MI_GWLOTO_CFG_ENABLE_GOOGLE_TRANSLATE_DSC','This will enable application access to Google AJAX Language API for Translation. This enables extra features for users with translation authorities. For terms and additional information see: http://code.google.com/apis/ajaxlanguage/');

define('_MI_GWLOTO_CFG_TCPDF_PATH','Path to TCPDF');
define('_MI_GWLOTO_CFG_TCPDF_PATH_DSC','TCPDF is required for most standard plugins. If it is installed, but not in a location that is auto-detected, please specify the full path (i.e. /full/path/to/tcpdf.php) here.');

// Blocks
define('_MI_GWLOTO_ASSIGNED_BLOCK', 'Assigned Jobs');
define('_MI_GWLOTO_ASSIGNED_BLOCK_DESC', 'Lists active job steps assigned to current user.');

?>