SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET default_storage_engine=INNODB;

-- FILES CLEANING: 
--    upload/report/*
--    upload/*NUMBER DIRECTORY ONLY*

-- CALENDAR CLEANING
TRUNCATE table `tevents`;
-- TICKET CLEANING
TRUNCATE table `tincidents`;
TRUNCATE table `ttemplates`;
TRUNCATE table `tthreads`;
TRUNCATE table `ttoken`;
-- NOTIFICATION CLEANING
TRUNCATE table `tmails`;
-- SURVEY CLEANING
TRUNCATE table `tsurvey_answers`;