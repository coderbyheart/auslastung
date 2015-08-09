CREATE TABLE organization_feed (
  id int(10) unsigned NOT NULL auto_increment,
  organization int(10) unsigned default NULL,
  operator int(10) unsigned default NULL COMMENT 'User who performs the action',
  subject int(10) unsigned default NULL COMMENT 'User who is affected by the action',
  action tinytext default NULL COMMENT 'Name of the action',
  z_ts_created datetime NULL default NULL,
  PRIMARY KEY  (id),
  KEY organization (organization),
  KEY operator (operator),
  KEY subject (subject),
  CONSTRAINT organization_feed_ibfk_1 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT organization_feed_ibfk_2 FOREIGN KEY (operator) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT organization_feed_ibfk_3 FOREIGN KEY (subject) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;