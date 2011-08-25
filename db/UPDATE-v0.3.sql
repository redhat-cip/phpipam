/************************
Update from v 0.3 to 0.4 
************************/

/* Add allowRequests and adminLock fields to subnets table */
ALTER TABLE `subnets` ADD `allowRequests` tinyint(1) DEFAULT '0';
ALTER TABLE `subnets` ADD `adminLock` binary(1) DEFAULT '0';

/* Add version field to settings */
ALTER TABLE `settings` ADD `version` varchar(4) DEFAULT NULL;
ALTER TABLE `settings` ADD `donate` tinyint(1) DEFAULT 0;

/* Add version */
UPDATE `settings` set `version` = '0.4' where `id` = '1';

/* Reset donations */
UPDATE `settings` set `donate` = '0'; 

/* Expand logs table */
ALTER TABLE `logs` ADD `details` varchar(1024) DEFAULT '0';