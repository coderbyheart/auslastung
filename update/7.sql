-- See #29

ALTER TABLE probability ADD textcolor char(6) default NULL AFTER color;
ALTER TABLE vacation_type ADD textcolor char(6) default NULL AFTER color; 
INSERT INTO sysconfig (name, value, z_ts_created) VALUES ('dbversion', '7', UTC_TIMESTAMP());