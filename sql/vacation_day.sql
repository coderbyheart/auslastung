CREATE TABLE vacation_day (
  id int(10) unsigned NOT NULL auto_increment,
  vacation int(10) unsigned default NULL,
  date date default NULL COMMENT 'Day',
  hours float(13,3) unsigned default NULL COMMENT 'Hours on Day',
  PRIMARY KEY  (id),
  KEY vacation (vacation),
  CONSTRAINT vacation_days_ibfk_1 FOREIGN KEY (vacation) REFERENCES vacation (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Hour information on vacation days.';