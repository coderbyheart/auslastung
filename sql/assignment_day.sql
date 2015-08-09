CREATE TABLE assignment_day (
  id int(10) unsigned NOT NULL auto_increment,
  assignment int(10) unsigned default NULL,
  date date default NULL COMMENT 'Day',
  hours float(13,3) unsigned default NULL COMMENT 'Hours on Day',
  PRIMARY KEY  (id),
  KEY assignment (assignment),
  CONSTRAINT assignment_days_ibfk_1 FOREIGN KEY (assignment) REFERENCES assignment (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Hour information on assignment days.';