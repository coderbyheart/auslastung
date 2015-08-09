-- Updates required for changes introduced with ticket #7

ALTER TABLE assignment ADD manual_end date default NULL COMMENT 'Manual set end date' AFTER end;
UPDATE assignment SET manual_end = end WHERE has_manual_end = '1';
ALTER TABLE assignment DROP has_manual_end;