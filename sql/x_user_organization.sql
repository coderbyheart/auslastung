CREATE TABLE x_user_organization (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned default NULL,
  organization int(10) unsigned default NULL,
  z_ts_created datetime NULL default NULL,
  z_ts_modified datetime NULL default NULL,
  PRIMARY KEY  (id),
  KEY user (user),
  KEY organization (organization),
  UNIQUE (user,organization),
  CONSTRAINT x_user_organization_ibfk_1 FOREIGN KEY (user) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT x_user_organization_ibfk_2 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;