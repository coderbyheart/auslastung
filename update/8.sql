-- See #26

ALTER TABLE assignment ADD author int(10) unsigned default NULL COMMENT 'User who created this entry';
ALTER TABLE assignment ADD CONSTRAINT assignment_ibfk_4 FOREIGN KEY (author) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE vacation ADD author int(10) unsigned default NULL COMMENT 'User who created this entry';
ALTER TABLE vacation ADD CONSTRAINT vacation_ibfk_3 FOREIGN KEY (author) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE;