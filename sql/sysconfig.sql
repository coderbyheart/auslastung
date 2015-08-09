CREATE TABLE sysconfig (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(255) default NULL,
  value varchar(255) default NULL,
  z_ts_created datetime NULL default NULL,
  z_ts_modified datetime NULL default NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='System configuration';