CREATE TABLE organization (
  id int(10) unsigned NOT NULL auto_increment,
  name text default NULL,
  z_created_by int(10) unsigned NULL default NULL,
  z_ts_created datetime NULL default NULL,
  z_ts_modified datetime NULL default NULL,
  PRIMARY KEY  (id),
  KEY z_created_by (z_created_by),
  CONSTRAINT organization_ibfk_1 FOREIGN KEY (z_created_by) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;