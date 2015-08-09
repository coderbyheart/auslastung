-- Updates required for changes introduced with ticket #3

CREATE TABLE discipline (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(255) default NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE x_discipline_unit (
  discipline int(10) unsigned default NULL,
  unit int(10) unsigned default NULL,
  CONSTRAINT x_discipline_unit_ibfk_1 FOREIGN KEY (discipline) REFERENCES discipline (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT x_discipline_unit_ibfk_2 FOREIGN KEY (unit) REFERENCES unit (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO discipline SELECT id,name FROM unit;

ALTER TABLE person ADD discipline int(10) unsigned default NULL AFTER unit;
UPDATE person SET discipline = unit;

DELETE FROM unit WHERE 1;

INSERT INTO unit (organization, name) VALUES ( 1, 'default unit' );

UPDATE person SET unit = ( SELECT MAX(id) FROM unit );

INSERT INTO x_discipline_unit SELECT id AS discipline, (SELECT MAX(id) FROM unit) AS unit FROM discipline;