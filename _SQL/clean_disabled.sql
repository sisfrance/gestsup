SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET default_storage_engine=INNODB;

-- DELETE ALL disabled tickets and asset
-- VERSION 1.0

-- TICKET CLEAN
DELETE FROM `tthreads` WHERE `ticket` IN (SELECT `id` FROM `tincidents` WHERE `disable`='1');
DELETE FROM `tevents` WHERE `incident` IN (SELECT `id` FROM `tincidents` WHERE `disable`='1');
DELETE FROM `tmails` WHERE `incident` IN (SELECT `id` FROM `tincidents` WHERE `disable`='1');
DELETE FROM `tsurvey_answers` WHERE `ticket_id` IN (SELECT `id` FROM `tincidents` WHERE `disable`='1');
DELETE FROM `ttoken` WHERE `ticket_id` IN (SELECT `id` FROM `tincidents` WHERE `disable`='1');
DELETE FROM `ttemplates` WHERE `incident` IN (SELECT `id` FROM `tincidents` WHERE `disable`='1');
DELETE FROM `tincidents` WHERE `disable`='1';

-- ASSET CLEAN
DELETE FROM `tassets_iface` WHERE `asset_id` IN (SELECT `id` FROM `tassets` WHERE `disable`='1' AND `id`!='0');
DELETE FROM `tassets_thread` WHERE `asset` IN (SELECT `id` FROM `tassets` WHERE `disable`='1' AND `id`!='0');
DELETE FROM `tassets` WHERE `disable`='1' AND `id`!='0';