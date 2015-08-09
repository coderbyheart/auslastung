CREATE TABLE project (
  id int(10) unsigned NOT NULL auto_increment,
  organization int(10) unsigned default NULL,
  name varchar(255) default NULL,
  PRIMARY KEY  (id),
  KEY organization (organization),
  CONSTRAINT project_ibfk_1 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
