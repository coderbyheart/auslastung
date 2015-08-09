CREATE TABLE person (
  id int(10) unsigned NOT NULL auto_increment,
  organization int(10) unsigned default NULL,
  unit int(10) unsigned default NULL,
  discipline int(10) unsigned default NULL,
  name varchar(255) default NULL,
  PRIMARY KEY  (id),
  KEY unit (unit),
  KEY person_ibfk_1 (organization),
  CONSTRAINT person_ibfk_1 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT person_ibfk_2 FOREIGN KEY (unit) REFERENCES unit (id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT person_ibfk_3 FOREIGN KEY (discipline) REFERENCES discipline (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;