#
# Tables for gwloto module
#

CREATE TABLE gwloto_media (
  media_id int(8) unsigned NOT NULL auto_increment,
  media_class ENUM('permit','form','diagram','instructions','manual','MSDS','other') NOT NULL default 'other',
  media_fileref int(8) unsigned NOT NULL,
  media_auth_place int(8) unsigned NOT NULL,
  PRIMARY KEY (media_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_media_file (
  media_file_id int(8) unsigned NOT NULL auto_increment,
  media_filename varchar(255) NOT NULL,
  media_storedname varchar(255) NOT NULL,
  media_mimetype varchar(255) NOT NULL,
  media_size int unsigned NOT NULL default '0',
  last_uploaded_by int(8) NOT NULL,
  last_uploaded_on int(10) NOT NULL,
  PRIMARY KEY (media_file_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_media_detail (
  media int(8) unsigned NOT NULL,
  language_id tinyint unsigned NOT NULL default '0',
  media_name varchar(100) NOT NULL,
  media_description text NOT NULL,
  media_lang_fileref int(8) unsigned NOT NULL default '0',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (media, language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_media_attach (
  media_attach_id int(8) unsigned NOT NULL auto_increment,
  attach_type ENUM('place','plan','point','job','jobstep') NOT NULL,
  generic_id int(8) unsigned NOT NULL,
  media_id int(8) unsigned NOT NULL,
  media_order int(8) unsigned NOT NULL default '0',
  required tinyint unsigned NOT NULL default '0',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (media_attach_id),
  UNIQUE KEY (attach_type, generic_id, media_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_place (
  place_id int(8) unsigned NOT NULL auto_increment,
  parent_id int(8) unsigned NOT NULL,
  PRIMARY KEY (place_id),
  KEY (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_place_detail (
  place int(8) unsigned NOT NULL,
  language_id tinyint unsigned NOT NULL default '0',
  place_name varchar(255) NOT NULL,
  place_hazard_inventory text NOT NULL,
  place_required_ppe text NOT NULL,
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (place, language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_language (
  language_id tinyint unsigned NOT NULL default '0',
  language varchar(100) NOT NULL,
  language_code varchar(20) NOT NULL,
  language_folder varchar(100) NOT NULL,
  PRIMARY KEY (language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_user (
  uid int(8) unsigned NOT NULL,
  language_id tinyint unsigned NOT NULL default '0',
  default_place_id int(8) unsigned NOT NULL default '0',
  supervisor_uid int(8) unsigned NOT NULL default '0',
  clipboard_id int(8) unsigned NOT NULL default '0',
  clipboard_type varchar(100) NOT NULL default '',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_user_auth (
  uid int(8) unsigned NOT NULL,
  place_id int(8) unsigned NOT NULL,
  authority int(8) unsigned NOT NULL default '0',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (uid, place_id, authority),
  KEY (authority,place_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_group_auth (
  groupid int(8) unsigned NOT NULL,
  place_id int(8) unsigned NOT NULL,
  authority int(8) unsigned NOT NULL default '0',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (groupid, place_id, authority),
  KEY (authority,place_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_cplan (
  cplan_id int(8) unsigned NOT NULL auto_increment,
  place_id int(8) unsigned NOT NULL,
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (cplan_id),
  KEY (place_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_cplan_detail (
  cplan int(8) unsigned NOT NULL,
  language_id tinyint unsigned NOT NULL default '0',
  cplan_name varchar(255) NOT NULL,
  cplan_review text NOT NULL,
  hazard_inventory text NOT NULL,
  required_ppe text NOT NULL,
  authorized_personnel text NOT NULL,
  additional_requirements text NOT NULL,
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (cplan, language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_cpoint (
  cpoint_id int(8) unsigned NOT NULL auto_increment,
  cplan_id int(8) unsigned NOT NULL,
  seq_disconnect int(8) NOT NULL,
  seq_reconnect int(8) NOT NULL,
  seq_inspection int(8) NOT NULL,
  locks_required tinyint unsigned NOT NULL,
  tags_required tinyint unsigned NOT NULL,
  PRIMARY KEY (cpoint_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_cpoint_detail (
  cpoint int(8) unsigned NOT NULL,
  language_id tinyint unsigned NOT NULL default '0',
  cpoint_name varchar(255) NOT NULL,
  disconnect_instructions text NOT NULL,
  disconnect_state varchar(255) NOT NULL,
  reconnect_instructions text NOT NULL,
  reconnect_state varchar(255) NOT NULL,
  inspection_instructions text NOT NULL,
  inspection_state varchar(255) NOT NULL,
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (cpoint, language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_job (
  job_id int(8) unsigned NOT NULL auto_increment,
  job_name varchar(255) NOT NULL,
  job_workorder varchar(255) NOT NULL,
  job_supervisor varchar(255) NOT NULL,
  job_startdate varchar(100) NOT NULL,
  job_enddate varchar(100) NOT NULL,
  job_description text NOT NULL,
  job_status ENUM('planning','active','complete','canceled') NOT NULL default 'planning',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (job_id),
  UNIQUE KEY (job_status, job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_job_steps (
  job_step_id int(8) unsigned NOT NULL auto_increment,
  job int(8) unsigned NOT NULL,
  cplan int(8) unsigned NOT NULL,
  step_name varchar(255) NOT NULL,
  assigned_uid int(8) unsigned NOT NULL,
  job_step_status ENUM('planning','disconnecting','disconnected','reconnecting','reconnected','inspecting','inspected','complete','canceled') NOT NULL default 'planning',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (job_step_id),
  UNIQUE KEY (job, cplan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_job_places (
  job int(8) unsigned NOT NULL,
  cplan int(8) unsigned NOT NULL,
  place int(8) unsigned NOT NULL,
  PRIMARY KEY (job, cplan, place)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_plugin_register (
  plugin_id int(8) unsigned NOT NULL auto_increment,
  plugin_type ENUM('jobprint','unknown') NOT NULL default 'unknown',
  plugin_seq int(8) NOT NULL,
  plugin_link varchar(255) NOT NULL,
  plugin_filename varchar(255) NOT NULL,
  plugin_language_filename varchar(255) NOT NULL,
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (plugin_id),
  UNIQUE KEY (plugin_type, plugin_seq, plugin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwloto_plugin_name (
  plugin int(8) unsigned NOT NULL,
  language_id tinyint unsigned NOT NULL default '0',
  plugin_name varchar(255) NOT NULL,
  plugin_description text NOT NULL,
  PRIMARY KEY (plugin, language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

