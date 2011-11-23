/************************
Update from v 0.3 to 0.5 
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

# Dump of table requests
# ------------------------------------------------------------
DROP TABLE IF EXISTS `switches`;

CREATE TABLE `switches` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hostname` varchar(32) DEFAULT NULL,
  `ip_addr` varchar(100) DEFAULT NULL,
  `vendor` varchar(156) DEFAULT NULL,
  `model` varchar(124) DEFAULT NULL,
  `version` varchar(128) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hostname` (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;