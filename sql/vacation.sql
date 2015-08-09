CREATE TABLE vacation (
  id int(10) unsigned NOT NULL auto_increment,
  person int(10) unsigned default NULL,
  type int(10) unsigned default NULL,
  start date default NULL COMMENT 'Start date',
  end date default NULL COMMENT 'End date',
  days float(13,3) default NULL COMMENT 'Hours in Days',
  duration int(10) unsigned default NULL COMMENT 'Duration in hours',
  description text,
  author int(10) unsigned default NULL COMMENT 'User who created this entry',
  PRIMARY KEY  (id),
  KEY person (person),
  CONSTRAINT vacation_ibfk_1 FOREIGN KEY (person) REFERENCES person (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT vacation_ibfk_2 FOREIGN KEY (type) REFERENCES vacation_type (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT vacation_ibfk_3 FOREIGN KEY (author) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Peoples vacations.';