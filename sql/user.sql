CREATE TABLE user (
  id int(10) unsigned NOT NULL auto_increment,
  email varchar(255) default NULL,
  password varchar(255) default NULL,
  name varchar(255) default NULL,
  is_active enum('0', '1') default '1',
  z_ts_created datetime NULL default NULL,
  z_ts_modified datetime NULL default NULL,
  z_ts_last_login datetime NULL default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='Users';