CREATE TABLE vacation_type (
  id int(10) unsigned NOT NULL auto_increment,
  organization int(10) unsigned default NULL,
  name varchar(255) default NULL,
  color char(6) default NULL,
  textcolor char(6) default NULL,
  PRIMARY KEY  (id),
  KEY organization (organization),
  CONSTRAINT vacation_type_ibfk_1 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;