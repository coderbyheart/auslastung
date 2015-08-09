CREATE TABLE user_config (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned default NULL,
  name varchar(255) default NULL,
  value varchar(255) default NULL,
  z_ts_created datetime NULL default NULL,
  z_ts_modified datetime NULL default NULL,
  PRIMARY KEY  (id),
  KEY user (user),
  CONSTRAINT user_config_ibfk_1 FOREIGN KEY (user) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;