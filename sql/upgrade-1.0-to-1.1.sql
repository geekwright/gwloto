#
# Upgrade from gwloto 1.0 to 1.1
#

CREATE TABLE gwloto_group_auth (
  groupid int(8) unsigned NOT NULL,
  place_id int(8) unsigned NOT NULL,
  authority int(8) unsigned NOT NULL default '0',
  last_changed_by int(8) NOT NULL,
  last_changed_on int(10) NOT NULL,
  PRIMARY KEY (groupid, place_id, authority),
  KEY (authority,place_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;