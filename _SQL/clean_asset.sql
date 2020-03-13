SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET default_storage_engine=INNODB;

-- FILES CLEANING: 
--    upload/asset/*

-- ASSET CLEANING
DELETE FROM `tassets` WHERE id!=0;
TRUNCATE table `tassets_iface`;
TRUNCATE table `tassets_thread`;