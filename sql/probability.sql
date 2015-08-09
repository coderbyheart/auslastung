CREATE TABLE probability (
  id int(10) unsigned NOT NULL auto_increment,
  organization int(10) unsigned default NULL,
  name varchar(255) default NULL,
  percentage int(3) unsigned default '100',
  color char(6) default NULL,
  textcolor char(6) default NULL,
  PRIMARY KEY  (id),
  KEY organization (organization),
  CONSTRAINT probability_ibfk_1 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;