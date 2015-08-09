-- Updates required for changes introduced with ticket #4

CREATE TABLE holiday (
  id int(10) unsigned NOT NULL auto_increment,
  country char(2) default NULL COMMENT 'Land',
  day date default NULL COMMENT 'Start date',
  description text,
  PRIMARY KEY  (id),
  KEY country (country),
  KEY day (day)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='contains a countries holidays';