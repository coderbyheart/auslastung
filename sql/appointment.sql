CREATE TABLE appointment (
  id int(10) unsigned NOT NULL auto_increment,
  organization int(10) unsigned default NULL,
  start datetime default NULL COMMENT 'Start date',
  end datetime default NULL COMMENT 'End date',
  duration int(10) unsigned default NULL COMMENT 'Duration in hours',
  is_holiday ENUM( '0', '1' ) NOT NULL DEFAULT '0' COMMENT 'Appointment is a holiday',
  description text,
  author int(10) unsigned default NULL COMMENT 'User who created this entry',
  PRIMARY KEY  (id),
  KEY organization (organization),
  KEY author (author),
  CONSTRAINT appointment_ibfk_1 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT appointment_ibfk_2 FOREIGN KEY (author) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Define appointments and holiday.';