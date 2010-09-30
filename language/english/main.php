<?php
if (!defined('XOOPS_ROOT_PATH')) die('Root path not defined');
define('_MD_GWLOTO_TITLE','geekwright Lockout Tagout');
define('_MD_GWLOTO_TITLE_SHORT','gwloto : '); // prepends to html page title

define('_MD_GWLOTO_TITLE_INDEX','Place Browser');
define('_MD_GWLOTO_TITLE_EDITAUTHS','Set User Authorities');
define('_MD_GWLOTO_TITLE_SELECT',   'Clipboard Actions');

define('_MD_GWLOTO_TITLE_NEWJOB',   'Start New Energy Control Job');
define('_MD_GWLOTO_TITLE_PRINTJOB', 'Print Energy Control Job Documents');
define('_MD_GWLOTO_TITLE_VIEWJOB',  'Energy Control Job Overview');
define('_MD_GWLOTO_TITLE_VIEWSTEP', 'Energy Control Job Step Detail');

define('_MD_GWLOTO_TITLE_EDITPLACE','Edit Places');
define('_MD_GWLOTO_TITLE_NEWPLACE', 'Add a New Place');

define('_MD_GWLOTO_TITLE_VIEWPLAN', 'View Energy Control Plan');
define('_MD_GWLOTO_TITLE_EDITPLAN', 'Edit Energy Control Plan');
define('_MD_GWLOTO_TITLE_NEWPLAN',  'Start New Energy Control Plan');

define('_MD_GWLOTO_TITLE_VIEWPOINT','View Energy Control Point');
define('_MD_GWLOTO_TITLE_EDITPOINT','Edit Energy Control Point');
define('_MD_GWLOTO_TITLE_NEWPOINT', 'Add New Energy Control Point');
define('_MD_GWLOTO_TITLE_SORTPOINT','Reorder Energy Control Points');

define('_MD_GWLOTO_TITLE_VIEWMEDIA','Media Details');
define('_MD_GWLOTO_TITLE_NEWMEDIA', 'Add New Media File');
define('_MD_GWLOTO_TITLE_LISTMEDIA','Browse Media Files');
define('_MD_GWLOTO_TITLE_ATTACHMEDIA','Attach Media File');

define('_MD_GWLOTO_TITLE_SORTPLUGINS','Reorder Plugins');
define('_MD_GWLOTO_TITLE_EDITPLUGIN','Edit Plugin');

define('_MD_GWLOTO_MSG_NO_ACCESS','You do not have any defined access. Please contact your supervisor.');
define('_MD_GWLOTO_MSG_ANON_ACCESS','You are not currently signed in, and no access for anonymous users is defined.');
define('_MD_GWLOTO_MSG_NO_AUTHORITY','You do not have the required authority to access the requested resources.');
define('_MD_GWLOTO_MSG_BAD_PARMS','Invalid parameters for operation.');
define('_MD_GWLOTO_MSG_BAD_TOKEN','Expired or invalid security token in request.');
define('_MD_GWLOTO_MSG_NO_TRANSLATE_DEFAULT','Translate authority cannot be used to alter default language versions.');

define('_MD_GWLOTO_ALL_AUTH_PLACES', 'All Places');

define('_MD_GWLOTO_LANG_TRAY', 'Language');
define('_MD_GWLOTO_LANG_CHANGE_BUTTON', 'Reload');
define('_MD_GWLOTO_LANG_TRANS_BUTTON', 'Translate');

// user authority form
define('_MD_GWLOTO_USERAUTH_FORM', 'Set User Authorities');
define('_MD_GWLOTO_USERAUTH_USER', 'Select User');
define('_MD_GWLOTO_USERAUTH_DISPLAY', 'Show current authorities');
define('_MD_GWLOTO_USERAUTH_AUTHS', 'Authorities');
define('_MD_GWLOTO_USERAUTH_DISPLAY_BUTTON', 'Show');
define('_MD_GWLOTO_USERAUTH_UPDATE', 'Save changes');
define('_MD_GWLOTO_USERAUTH_UPDATE_BUTTON', 'Save');
define('_MD_GWLOTO_USERAUTH_UPDATE_OK', 'Authorities saved. ');
define('_MD_GWLOTO_USERAUTH_DB_ERROR', 'Could not save Authorities. ');

define('_MD_GWLOTO_USERAUTH_RPT_TITLE', 'Active Authorities for User by Place');
define('_MD_GWLOTO_USERAUTH_RPT_PLACE', 'Place');
define('_MD_GWLOTO_USERAUTH_RPT_AUTHS', 'Authority');
define('_MD_GWLOTO_USERAUTH_RPT_NOAUTH', 'No Authorities');

define('_MD_GWLOTO_USERAUTH_EXIT', 'Exit Authority Editor');


define('_MD_GWLOTO_LASTCHG_BY', 'Last Updated By');
define('_MD_GWLOTO_LASTCHG_ON', 'Last Update Time');

// place form
define('_MD_GWLOTO_EDITPLACE_FORM', 'Edit Place');

define('_MD_GWLOTO_EDITPLACE_NAME', 'Place Name');
define('_MD_GWLOTO_EDITPLACE_HAZARDS', 'Hazard Inventory');
define('_MD_GWLOTO_EDITPLACE_PPE', 'Required Personal Protective Equipment');

define('_MD_GWLOTO_EDITPLACE_UPDATE', 'Save changes');
define('_MD_GWLOTO_EDITPLACE_UPDATE_BUTTON', 'Save');
define('_MD_GWLOTO_EDITPLACE_UPDATE_OK', 'Place saved. ');
define('_MD_GWLOTO_EDITPLACE_DB_ERROR', 'Could not save Place. ');
define('_MD_GWLOTO_EDITPLACE_NOTFOUND', 'Place not found. ');

define('_MD_GWLOTO_PLACE_HAZARDS', 'Hazard Inventory for %s');
define('_MD_GWLOTO_PLACE_PPE', 'Required PPE for %s');
define('_MD_GWLOTO_NO_PLACE_HAZARDS', '<i>(none defined)</i>');
define('_MD_GWLOTO_NO_PLACE_PPE', '<i>(none defined)</i>');
// new place form
define('_MD_GWLOTO_NEWPLACE_FORM', 'Add Sub-Place below %s');

define('_MD_GWLOTO_NEWPLACE_ADD_BUTTON_DSC', 'Add New Place');
define('_MD_GWLOTO_NEWPLACE_ADD_BUTTON', 'Add');
define('_MD_GWLOTO_NEWPLACE_ADD_OK', 'Place added. ');
define('_MD_GWLOTO_NEWPLACE_DB_ERROR', 'Could not add Place. ');

// control plan form
define('_MD_GWLOTO_EDITPLAN_FORM', 'Edit Control Plan');

define('_MD_GWLOTO_EDITPLAN_NAME', 'Control Plan Name');
define('_MD_GWLOTO_EDITPLAN_REVIEW', 'Reviews / Approvals');
define('_MD_GWLOTO_EDITPLAN_HAZARDS', 'Hazard Inventory');
define('_MD_GWLOTO_EDITPLAN_PPE', 'Required Personal Protective Equipment');
define('_MD_GWLOTO_EDITPLAN_AUTHPERSONNEL', 'Authorized / Required Personnel');
define('_MD_GWLOTO_EDITPLAN_ADDREQ', 'Additional Requirements');

define('_MD_GWLOTO_EDITPLAN_UPDATE', 'Save changes');
define('_MD_GWLOTO_EDITPLAN_UPDATE_BUTTON', 'Save');
define('_MD_GWLOTO_EDITPLAN_UPDATE_OK', 'Plan saved. ');
define('_MD_GWLOTO_EDITPLAN_DB_ERROR', 'Could not save Plan. ');
define('_MD_GWLOTO_EDITPLAN_NOTFOUND', 'Plan not found. ');

define('_MD_GWLOTO_NEWPLAN_FORM', 'Add New Control Plan to %s');
define('_MD_GWLOTO_VIEWPLAN_FORM', 'Control Plan');
define('_MD_GWLOTO_VIEWPLAN_COUNTS', 'Counts');
define('_MD_GWLOTO_VIEWPLAN_COUNTS_DETAIL', 'Points=%1$d Tags=%2$d Locks=%3$d ');
define('_MD_GWLOTO_VIEWPLAN_TRANSLATE_STATS', 'Translation Stats ');
define('_MD_GWLOTO_VIEWPLAN_SEQ', 'Point Sequence');

define('_MD_GWLOTO_NEWPLAN_ADD_BUTTON_DSC', 'Add New Control Plan');
define('_MD_GWLOTO_NEWPLAN_ADD_BUTTON', 'Add');
define('_MD_GWLOTO_NEWPLAN_ADD_OK', 'Plan added. ');
define('_MD_GWLOTO_NEWPLAN_DB_ERROR', 'Could not add Control Plan. ');

define('_MD_GWLOTO_CPOINT_RPT_TITLE', 'Control Points');
define('_MD_GWLOTO_CPOINT_RPT_NAME', 'Point Name');
define('_MD_GWLOTO_CPOINT_RPT_DISC_INST','Disconnect Instructions');
define('_MD_GWLOTO_CPOINT_RPT_DISC_STATE','Disconnected');
define('_MD_GWLOTO_CPOINT_RPT_LOCKS_REQ','Locks');
define('_MD_GWLOTO_CPOINT_RPT_TAGS_REQ','Tags');
define('_MD_GWLOTO_CPOINT_RPT_RECON_INST','Reconnect Instructions');
define('_MD_GWLOTO_CPOINT_RPT_RECON_STATE','Connected');
define('_MD_GWLOTO_CPOINT_RPT_INSP_INST','Inspection Instructions');

// Control Point Form
define('_MD_GWLOTO_NEWPOINT_FORM', 'New Control Point - %s');
define('_MD_GWLOTO_EDITPOINT_FORM', 'Edit Control Point');
define('_MD_GWLOTO_EDITPOINT_NAME', 'Control Point Name');
define('_MD_GWLOTO_EDITPOINT_DISC_INST','Disconnect Instructions');
define('_MD_GWLOTO_EDITPOINT_DISC_STATE','Disconnect State');
define('_MD_GWLOTO_EDITPOINT_LOCKS_REQ','Locks Required');
define('_MD_GWLOTO_EDITPOINT_TAGS_REQ','Number of Tags Copies');
define('_MD_GWLOTO_EDITPOINT_RECON_INST','Reconnect Instructions');
define('_MD_GWLOTO_EDITPOINT_RECON_STATE','Reconnect State');
define('_MD_GWLOTO_EDITPOINT_INSP_INST','Inspection Instructions');
define('_MD_GWLOTO_EDITPOINT_INSP_STATE','Inspection State');

define('_MD_GWLOTO_NEWPOINT_ADD_BUTTON_DSC', 'Add New Control Point');
define('_MD_GWLOTO_NEWPOINT_ADD_BUTTON', 'Add');
define('_MD_GWLOTO_NEWPOINT_ADD_OK', 'Control Point added.');
define('_MD_GWLOTO_NEWPOINT_DB_ERROR', 'Could not add Control Point. ');

define('_MD_GWLOTO_VIEWPOINT_FORM', 'Control Point');

define('_MD_GWLOTO_EDITPOINT_UPDATE', 'Save changes');
define('_MD_GWLOTO_EDITPOINT_UPDATE_BUTTON', 'Save');
define('_MD_GWLOTO_EDITPOINT_UPDATE_OK', 'Control Point saved.');
define('_MD_GWLOTO_EDITPOINT_DB_ERROR', 'Could not save Control Point.');
define('_MD_GWLOTO_EDITPOINT_NOTFOUND', 'Control Point not found. ');

// Job Forms
define('_MD_GWLOTO_JOB_NEW_FORM', 'New Job');
define('_MD_GWLOTO_JOB_EDIT_FORM', 'Edit Job');
define('_MD_GWLOTO_JOB_VIEW_FORM', 'Job Details');
define('_MD_GWLOTO_JOB_PRINT_FORM', 'Job selected for Printing');

define('_MD_GWLOTO_JOB_ADD_BUTTON_DSC', 'Add New Job');
define('_MD_GWLOTO_JOB_ADD_BUTTON', 'Add');
define('_MD_GWLOTO_JOB_ADD_OK', 'Job added.');
define('_MD_GWLOTO_JOB_ADD_DB_ERROR', 'Could not add Job. ');

define('_MD_GWLOTO_JOBSTEP_NEW_FORM', 'Add Job Step');
define('_MD_GWLOTO_JOBSTEP_EDIT_FORM', 'Edit Job Step');
define('_MD_GWLOTO_JOBSTEP_VIEW_FORM', 'Job Step Details');

define('_MD_GWLOTO_JOB_TOOL_TRAY_DSC', 'Job Tools');
define('_MD_GWLOTO_JOB_EDIT_BUTTON', 'Save');
define('_MD_GWLOTO_JOB_EDIT_OK', 'Job saved.');
define('_MD_GWLOTO_JOB_EDIT_DB_ERROR', 'Could not save Job.');

define('_MD_GWLOTO_JOBSTEP_TOOL_TRAY_DSC', 'Job Step Tools');
define('_MD_GWLOTO_JOBSTEP_EDIT_BUTTON', 'Save');
define('_MD_GWLOTO_JOBSTEP_ADD_BUTTON', 'Add Step');
define('_MD_GWLOTO_JOBSTEP_ADD_PICK_MSG', 'Locate control plan to add to job');
define('_MD_GWLOTO_JOBSTEP_ADD_OK', 'Job step added.');
define('_MD_GWLOTO_JOBSTEP_ADD_DB_ERROR', 'Could not add job step. ');
define('_MD_GWLOTO_JOBSTEP_EDIT_OK', 'Job step saved.');
define('_MD_GWLOTO_JOBSTEP_EDIT_DB_ERROR', 'Could not save job step. ');
define('_MD_GWLOTO_JOBSTEP_DUPLICATE_PLAN', 'Selected control plan is already part of job.');

define('_MD_GWLOTO_JOB_NOTFOUND', 'Job not found. ');

define('_MD_GWLOTO_JOB_RPT_TITLE', 'Available Jobs');
define('_MD_GWLOTO_JOB_NAME', 'Job Name');
define('_MD_GWLOTO_JOB_WORKORDER', 'Work Order');
define('_MD_GWLOTO_JOB_SUPERVISOR', 'Supervisor');
define('_MD_GWLOTO_JOB_PICKSUPER', 'Pick Supervisor');
define('_MD_GWLOTO_JOB_STARTDATE', 'Planned Start Date');
define('_MD_GWLOTO_JOB_ENDDATE', 'Expected Completion Date');
define('_MD_GWLOTO_JOB_DESCRIPTION', 'Job Description');

define('_MD_GWLOTO_JOB_STATUS', 'Job Status');
define('_MD_GWLOTO_JOB_STATUS_PLANNING', 'Planning');
define('_MD_GWLOTO_JOB_STATUS_ACTIVE', 'Active');
define('_MD_GWLOTO_JOB_STATUS_COMPLETE', 'Complete');
define('_MD_GWLOTO_JOB_STATUS_CANCELED', 'Canceled');

define('_MD_GWLOTO_JOBSTEP_NAME', 'Step Name');
define('_MD_GWLOTO_JOBSTEP_ASSIGNED_UID', 'Assigned To');
define('_MD_GWLOTO_JOBSTEP_PLAN', 'Control Plan');

define('_MD_GWLOTO_JOBSTEP_STATUS', 'Job Step Status');
define('_MD_GWLOTO_JOBSTEP_STATUS_PLANNING', 'Planning');
define('_MD_GWLOTO_JOBSTEP_STATUS_WIP_DISC', 'Disconnect In Processes');
define('_MD_GWLOTO_JOBSTEP_STATUS_DISC', 'Disconnected');
define('_MD_GWLOTO_JOBSTEP_STATUS_WIP_RECON', 'Reconnect In Processes');
define('_MD_GWLOTO_JOBSTEP_STATUS_RECON', 'Reconnected');
define('_MD_GWLOTO_JOBSTEP_STATUS_WIP_INSP', 'Inspection In Processes');
define('_MD_GWLOTO_JOBSTEP_STATUS_INSP', 'Inspected');
define('_MD_GWLOTO_JOBSTEP_STATUS_COMPLETE', 'Complete');
define('_MD_GWLOTO_JOBSTEP_STATUS_CANCELED', 'Canceled');

define('_MD_GWLOTO_STEP_RPT_TITLE', 'Job Steps');
define('_MD_GWLOTO_STEP_NAME', 'Step');
define('_MD_GWLOTO_STEP_STATUS', 'Status');
define('_MD_GWLOTO_STEP_CPLAN', 'Plan Name');
define('_MD_GWLOTO_STEP_ASSIGNED', 'Assigned');

define('_MD_GWLOTO_JOB_PHASE_SEQ', 'Print for Phase');
define('_MD_GWLOTO_JOB_PRINT_BUTTON', 'Print');
define('_MD_GWLOTO_JOB_PREDIT_BUTTON', 'Edit');
define('_MD_GWLOTO_JOB_PRVIEW_BUTTON', 'View');
define('_MD_GWLOTO_JOB_PRINT_PICK', 'Select what to print');
define('_MD_GWLOTO_JOB_PRINT_REDIR_MSG', 'Choose print options.');
define('_MD_GWLOTO_JOB_PRINTING_REDIR_MSG', 'Printing.');
define('_MD_GWLOTO_JOB_VIEW_REDIR_MSG', 'Job details displayed.');
define('_MD_GWLOTO_NEED_TCPDF', 'Cannot locate required TCPDF class');
define('_MD_GWLOTO_JOB_PRINT_NODEFS', 'No print plugins are installed. Notify the system administrator.');

// user authorities
define('_MD_GWLOTO_USERAUTH_PL_ADMIN_DSC', 'Edit User Authorities');
define('_MD_GWLOTO_USERAUTH_PL_AUDIT_DSC', 'View User Authorities');
define('_MD_GWLOTO_USERAUTH_PL_EDIT_DSC',  'Add or Edit Places');
define('_MD_GWLOTO_USERAUTH_PL_SUPER_DSC', 'Supervisor of Place');

define('_MD_GWLOTO_USERAUTH_CP_EDIT_DSC',  'Add or Edit Control Plans');
define('_MD_GWLOTO_USERAUTH_CP_VIEW_DSC',  'View Control Plans');

define('_MD_GWLOTO_USERAUTH_JB_EDIT_DSC',  'Add or Edit Jobs');
define('_MD_GWLOTO_USERAUTH_JB_VIEW_DSC',  'View Jobs');

define('_MD_GWLOTO_USERAUTH_MD_EDIT_DSC',  'Add or Edit Media');
define('_MD_GWLOTO_USERAUTH_MD_VIEW_DSC',  'Browse Media');

define('_MD_GWLOTO_USERAUTH_PL_TRANS_DSC', 'Translate Places');
define('_MD_GWLOTO_USERAUTH_CP_TRANS_DSC', 'Translate Control Plans');
define('_MD_GWLOTO_USERAUTH_MD_TRANS_DSC', 'Translate Media');

define('_MD_GWLOTO_SORTPOINT_FORM', 'Sort Control Points - %s');
define('_MD_GWLOTO_SORTPOINT_UP', 'Move Up');
define('_MD_GWLOTO_SORTPOINT_DOWN', 'Move Down');
define('_MD_GWLOTO_SORTPOINT_REVERSE', 'Reverse');
define('_MD_GWLOTO_SORTPOINT_SAVE', 'Save');
define('_MD_GWLOTO_SORTPOINT_SELECT', 'Select a Control Point to Move');
define('_MD_GWLOTO_SORTPOINT_ACTIONS', 'Actions');
define('_MD_GWLOTO_SORTPOINT_CPOINTS', 'Control Points');
define('_MD_GWLOTO_SORTPOINT_SEQ', 'Sequence to Set');
define('_MD_GWLOTO_SORTPOINT_SEQ_DISCON', 'Disconnect');
define('_MD_GWLOTO_SORTPOINT_SEQ_RECON', 'Reconnect');
define('_MD_GWLOTO_SORTPOINT_SEQ_INSPECT', 'Inspection');
define('_MD_GWLOTO_SORTPOINT_SEQ_SHOW', 'Re-Show');
define('_MD_GWLOTO_SORTPOINT_EMPTY', 'Nothing to Sort');

define('_MD_GWLOTO_SETHOME_OK', 'Set Home Place');
// program link descriptions
define('_MD_GWLOTO_PRG_DSC_EDITAUTHS', 'User Authorities');
define('_MD_GWLOTO_PRG_DSC_EDITPLACE', 'Edit this Place');
define('_MD_GWLOTO_PRG_DSC_ADDPLACE', 'Add a Place');
define('_MD_GWLOTO_PRG_DSC_ADDPLAN', 'Add a Control Plan');
define('_MD_GWLOTO_PRG_DSC_VIEWPLAN', 'View Control Plan');
define('_MD_GWLOTO_PRG_DSC_EDITPLAN', 'Edit this Control Plan');
define('_MD_GWLOTO_PRG_DSC_ADDPOINT', 'Add a Control Point');
define('_MD_GWLOTO_PRG_DSC_EDITPOINT', 'Edit this Control Point');
define('_MD_GWLOTO_PRG_DSC_SRTPOINT', 'Sort Control Points');
define('_MD_GWLOTO_PRG_DSC_SETHOME', 'Set As Home');
define('_MD_GWLOTO_PRG_DSC_SELPLACE', 'Select Place');
define('_MD_GWLOTO_PRG_DSC_SELPLAN', 'Select Control Plan');
define('_MD_GWLOTO_PRG_DSC_SELPOINT', 'Select Control Point');
define('_MD_GWLOTO_PRG_DSC_SELMEDIA', 'Select Media');
define('_MD_GWLOTO_PRG_DSC_NEWJOB', 'New Job with this Plan');
define('_MD_GWLOTO_PRG_DSC_MEDIA', 'Media Center');

define('_MD_GWLOTO_CHOOSE_PLACE', 'Choose Location');
define('_MD_GWLOTO_CHOOSE_ACTION', 'Actions Menu');
define('_MD_GWLOTO_CHOOSE_CTRLPLAN', 'Control Plans for this Location');

define('_MD_GWLOTO_CHOOSE_SELECTED', 'Choose Action for Selected Item');
define('_MD_GWLOTO_CLIPBOARD_FORM', 'Choose Action for Clipboard Item');
define('_MD_GWLOTO_DELETE_SELECTED', 'Delete this Item');
define('_MD_GWLOTO_MOVE_SELECTED', 'Move this Item');
define('_MD_GWLOTO_COPY_SELECTED', 'Copy this Item');
define('_MD_GWLOTO_MOVECOPY_SELECTED', 'Move/Copy this Item');
define('_MD_GWLOTO_CANCEL_SELECTED', 'Cancel Move/Copy');
define('_MD_GWLOTO_DELETE_SEL_BUTTON', 'Delete');
define('_MD_GWLOTO_MOVE_SEL_BUTTON', 'Move');
define('_MD_GWLOTO_COPY_SEL_BUTTON', 'Copy');
define('_MD_GWLOTO_CANCEL_SEL_BUTTON', 'Cancel');
define('_MD_GWLOTO_MOVECOPY_SEL_BUTTON', 'Move/Copy');
define('_MD_GWLOTO_MOVECOPY_SEL_OK', 'Copied to Clipboard. Select destination for Move or Copy.');
define('_MD_GWLOTO_DELETE_SEL_CONFIRM', 'Are you sure you want to delete this?');

define('_MD_GWLOTO_CANCEL_SEL_OK', 'Clipboard Emptied');
define('_MD_GWLOTO_COPY_SEL_OK', 'Copied');
define('_MD_GWLOTO_COPY_SEL_ERR', 'Could Not Copy');
define('_MD_GWLOTO_MOVE_SEL_OK', 'Moved');
define('_MD_GWLOTO_MOVE_SEL_ERR', 'Could Not Move');
define('_MD_GWLOTO_DELETE_SEL_OK', 'Deleted');
define('_MD_GWLOTO_DELETE_SEL_ERR', 'Could Not Delete');
define('_MD_GWLOTO_DELETE_SEL_PLACE_IN_USE', 'Could Not Delete. %1$d sub-locations and %2$d control plans are attached');
define('_MD_GWLOTO_MOVE_SEL_ONLY_TOP_PLACE', 'Can Not Move Only Top Level Place');
define('_MD_GWLOTO_COPY_NAME_PREFIX', 'Copy of ');

define('_MD_GWLOTO_CLIPBOARD_JOB_FORM', 'Add Control Plan to Job');
define('_MD_GWLOTO_STEP_ADD_THIS_BUTTON', 'Add to Job');
define('_MD_GWLOTO_STEP_ADD_CANCEL_BUTTON', 'Cancel');

// media
define('_MD_GWLOTO_MEDIA_RPT_TITLE', 'Available Media');

define('_MD_GWLOTO_MEDIA_FILE_TO_UPLOAD', 'File to Add');
define('_MD_GWLOTO_MEDIA_LINK', 'Link to Media');
define('_MD_GWLOTO_MEDIA_CLASS', 'Classification');
define('_MD_GWLOTO_MEDIA_CLASS_SELECT', 'Select Classification');
define('_MD_GWLOTO_MEDIA_NAME', 'Media Name');
define('_MD_GWLOTO_MEDIA_DESCRIPTION', 'Description');
define('_MD_GWLOTO_MEDIA_REQUIRED', 'Required');
define('_MD_GWLOTO_MEDIA_ADD_BUTTON_DSC', 'Add Media File');
define('_MD_GWLOTO_MEDIA_ADD_BUTTON', 'Upload');
define('_MD_GWLOTO_MEDIA_ADD_FORM', 'Upload New Media File');
define('_MD_GWLOTO_MEDIA_VIEW_FORM', 'View Media Detail');
define('_MD_GWLOTO_MEDIA_EDIT_FORM', 'Edit Media Detail');
define('_MD_GWLOTO_MEDIA_ATTACH_FORM', 'Attach Media Detail');

define('_MD_GWLOTO_ATTACHED_MEDIA_TITLE', 'Attached Media');
define('_MD_GWLOTO_ATTACH_MEDIA', 'Attach Media');

define('_MD_GWLOTO_MEDIA_AUTHPLACE', 'Place for Authority');
define('_MD_GWLOTO_MEDIA_AUTHPLACE_CHOOSE', 'Choose Place');

define('_MD_GWLOTO_MEDIA_ADDNEW', 'Add');
define('_MD_GWLOTO_MEDIA_BROWSE', 'Browse');
define('_MD_GWLOTO_MEDIA_EXIT', 'Exit Media Center');
define('_MD_GWLOTO_MEDIA_SELECT', 'Select');
define('_MD_GWLOTO_MEDIA_SEARCH_BUTTON', 'Search');
define('_MD_GWLOTO_MEDIA_SELECT_BUTTON', 'Select');
define('_MD_GWLOTO_MEDIA_ATTACH_BUTTON', 'Attach');
define('_MD_GWLOTO_MEDIA_CANCEL_BUTTON', 'Cancel');
define('_MD_GWLOTO_MEDIA_DETACH_BUTTON', 'Detach');
define('_MD_GWLOTO_MEDIA_VIEW_FILE', 'View');
define('_MD_GWLOTO_MEDIA_SAVE_BUTTON', 'Save');
define('_MD_GWLOTO_MEDIA_DELETE_BUTTON', 'Delete');
define('_MD_GWLOTO_MEDIA_DELETE_CONFIRM', 'Are you sure you want to delete the selected item(s)?');
define('_MD_GWLOTO_MEDIA_TOOL_TRAY_DSC', 'Media Tools');

define('_MD_GWLOTO_MEDIA_SELECT_TO_ATTACH', 'Select media to attach.');
define('_MD_GWLOTO_MEDIA_SELECT_PROMPT', 'Selecting media to attach to %1$s %2$s');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_PLACE', 'place');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_PLAN', 'control plan');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_POINT', 'control point');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_JOB', 'job');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_JOBSTEP', 'job step');
define('_MD_GWLOTO_MEDIA_SELECT_CANCELED', 'Media selection canceled.');

define('_MD_GWLOTO_MEDIA_ATTACH_TO', '%1$s %2$s');
define('_MD_GWLOTO_MEDIA_ATTACH_TO_PROMPT', 'Attach To');
define('_MD_GWLOTO_MEDIA_ATTACH_OPTIONS', 'Options');
define('_MD_GWLOTO_MEDIA_ATTACH_REQUIRED', 'Check to marked as Required.');
define('_MD_GWLOTO_MEDIA_ATTACH_CONTINUE', 'Check to attach additional media.');
define('_MD_GWLOTO_MEDIA_ATTACH_OK', 'Media Attached.');
define('_MD_GWLOTO_MEDIA_ATTACH_DB_ERROR', 'Could not attach media.');
define('_MD_GWLOTO_MEDIA_DETACH_OK', 'Media detached.');
define('_MD_GWLOTO_MEDIA_DETACH_DB_ERROR', 'Could not detach media.');
define('_MD_GWLOTO_MEDIA_DETACH_CONFIRM', 'Are you sure you want to detach this?');

define('_MD_GWLOTO_MEDIA_FILE_NAME', 'Current File / Link');
define('_MD_GWLOTO_MEDIA_UPLOAD_BY', 'Uploaded By');
define('_MD_GWLOTO_MEDIA_UPLOAD_ON', 'Upload Date');

define('_MD_GWLOTO_MEDIA_NO_MEDIA', 'No media items are defined.');
define('_MD_GWLOTO_MEDIA_NO_MATCH', 'No media items were found that match the search criteria.');
define('_MD_GWLOTO_MEDIA_NOTFOUND', 'Media not found. ');

define('_MD_GWLOTO_MEDIA_FILE_NOT_GIVEN', 'Either a media file or a link must be specified.');
define('_MD_GWLOTO_MEDIA_FILE_UPLOAD_ERROR', 'Upload Failed - error code %1$d');
define('_MD_GWLOTO_MEDIA_FILE_MOVE_ERROR', 'Upload Failed - Is upload path writable?');
define('_MD_GWLOTO_MEDIA_ADD_OK', 'Media Added');
define('_MD_GWLOTO_MEDIA_ADD_DB_ERROR', 'Could not add media.');
define('_MD_GWLOTO_MEDIA_UPDATE_OK', 'Media Updated');
define('_MD_GWLOTO_MEDIA_UPDATE_DB_ERROR', 'Could not update media.');
define('_MD_GWLOTO_MEDIA_DELETE_OK', 'Media Deleted');
define('_MD_GWLOTO_MEDIA_DELETE_DB_ERROR', 'Could not delete media.');

//  media_class ENUM('permit','form','diagram','instructions','other')
define('_MD_GWLOTO_MEDIACLASS_PERMIT', 'Permit');
define('_MD_GWLOTO_MEDIACLASS_FORM', 'Form');
define('_MD_GWLOTO_MEDIACLASS_DIAGRAM', 'Diagram');
define('_MD_GWLOTO_MEDIACLASS_INSTRUCTIONS', 'Instructions');
define('_MD_GWLOTO_MEDIACLASS_MANUAL', 'Manual');
define('_MD_GWLOTO_MEDIACLASS_MSDS', 'Material Safety Data Sheet');
define('_MD_GWLOTO_MEDIACLASS_OTHER', 'Other');

// plugins
define('_MD_GWLOTO_PLUGIN_ADMIN', 'Plugin Admin');
define('_MD_GWLOTO_PLUGIN_NAME', 'Plugin Name');
define('_MD_GWLOTO_PLUGIN_DESCRIPTION', 'Description');
?>
