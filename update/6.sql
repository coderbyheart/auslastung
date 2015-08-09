-- Updates required for changes introduced with ticket #17

ALTER TABLE sysconfig ADD z_ts_created datetime NULL default NULL AFTER value;
ALTER TABLE sysconfig ADD z_ts_modified datetime NULL default NULL AFTER z_ts_created;

CREATE TABLE x_user_organization (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned default NULL,
  organization int(10) unsigned default NULL,
  z_ts_created datetime NULL default NULL,
  z_ts_modified datetime NULL default NULL,
  PRIMARY KEY  (id),
  KEY user (user),
  KEY organization (organization),
  CONSTRAINT x_user_organization_ibfk_1 FOREIGN KEY (user) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT x_user_organization_ibfk_2 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO user (email, password, name, z_ts_created) VALUES ('admin', '{SHA}+pvrmeQCmtWmYVOZ57uuITVghrM=', 'Default User', UTC_TIMESTAMP());

INSERT INTO x_user_organization (user, organization, is_owner, z_ts_created) VALUES ('1', '1', '1', UTC_TIMESTAMP());

ALTER TABLE organization ADD z_created_by int(10) unsigned NULL default NULL AFTER name;
ALTER TABLE organization ADD z_ts_created datetime NULL default NULL AFTER z_created_by;
ALTER TABLE organization ADD z_ts_modified datetime NULL default NULL AFTER z_ts_created;
ALTER TABLE organization ADD KEY z_created_by (z_created_by);
ALTER TABLE organization ADD CONSTRAINT organization_ibfk_1 FOREIGN KEY (z_created_by) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE organization DROP INDEX name;
ALTER TABLE organization CHANGE name name TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

CREATE TABLE user_config (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned default NULL,
  name varchar(255) default NULL,
  value varchar(255) default NULL,
  z_ts_created datetime NULL default NULL,
  z_ts_modified datetime NULL default NULL,
  PRIMARY KEY  (id),
  KEY user (user),
  CONSTRAINT user_config_ibfk_1 FOREIGN KEY (user) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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

INSERT INTO sysconfig (name, value, z_ts_created) VALUES ('dbversion', '6', UTC_TIMESTAMP());