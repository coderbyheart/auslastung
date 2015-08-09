-- Updates required for changes introduced with ticket #1

CREATE TABLE vacation_type (
  id int(10) unsigned NOT NULL auto_increment,
  organization int(10) unsigned default NULL,
  name varchar(255) default NULL,
  color char(6) default NULL,
  PRIMARY KEY  (id),
  KEY organization (organization),
  CONSTRAINT vacation_type_ibfk_1 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO vacation_type (id, organization, name, color) VALUES
(NULL, 1, 'Urlaub', '3274d1'),
(NULL, 1, 'Urlaub (geplant)', '32c4d1'),
(NULL, 1, 'Abwesenheit', 'ff4aff');

CREATE TABLE vacation (
  id int(10) unsigned NOT NULL auto_increment,
  person int(10) unsigned default NULL,
  type int(10) unsigned default NULL,
  start date default NULL COMMENT 'Start date',
  end date default NULL COMMENT 'End date',
  days float(13,3) default NULL COMMENT 'Hours in Days',
  duration int(10) unsigned default NULL COMMENT 'Duration in hours',
  description text,
  PRIMARY KEY  (id),
  KEY person (person),
  CONSTRAINT vacation_ibfk_1 FOREIGN KEY (person) REFERENCES person (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT vacation_ibfk_2 FOREIGN KEY (type) REFERENCES vacation_type (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Peoples vacations.';

CREATE TABLE vacation_day (
  id int(10) unsigned NOT NULL auto_increment,
  vacation int(10) unsigned default NULL,
  date date default NULL COMMENT 'Day',
  hours float(13,3) unsigned default NULL COMMENT 'Hours on Day',
  PRIMARY KEY  (id),
  KEY vacation (vacation),
  CONSTRAINT vacation_days_ibfk_1 FOREIGN KEY (vacation) REFERENCES vacation (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Hour information on vacation days.';

CREATE TABLE assignment_day (
  id int(10) unsigned NOT NULL auto_increment,
  assignment int(10) unsigned default NULL,
  date date default NULL COMMENT 'Day',
  hours float(13,3) unsigned default NULL COMMENT 'Hours on Day',
  PRIMARY KEY  (id),
  KEY assignment (assignment),
  CONSTRAINT assignment_days_ibfk_1 FOREIGN KEY (assignment) REFERENCES assignment (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Hour information on assignment days.';

CREATE TABLE sysconfig (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(255) default NULL,
  value varchar(255) default NULL,
  z_ts_created DATETIME NULL,
  z_ts_modified DATETIME NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='System configuration';

ALTER TABLE assignment ADD distribute_duration ENUM( '0', '1' ) NOT NULL DEFAULT '0' COMMENT 'Distribute duration between start and manual_end' AFTER manual_end;