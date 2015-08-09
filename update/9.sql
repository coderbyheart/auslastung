-- See #18
-- F'up to r150

ALTER TABLE discipline ADD organization int(10) unsigned default NULL AFTER id;
UPDATE discipline SET organization = 1;
ALTER TABLE discipline ADD CONSTRAINT discipline_ibfk_1 FOREIGN KEY (organization) REFERENCES organization (id) ON DELETE CASCADE ON UPDATE CASCADE;