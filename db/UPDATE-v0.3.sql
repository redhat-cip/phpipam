/************************
Update from v 0.3 to 0.5 
************************/


/* Add allowRequests and adminLock fields to subnets table */
ALTER TABLE `subnets` ADD `allowRequests` tinyint(1) DEFAULT '0';
ALTER TABLE `subnets` ADD `adminLock` binary(1) DEFAULT '0';
ALTER TABLE `subnets` ADD `vrfId` int(3) DEFAULT NULL after `VLAN`;

/* Add version field to settings */
ALTER TABLE `settings` ADD `version` varchar(4) DEFAULT NULL;
ALTER TABLE `settings` ADD `donate` tinyint(1) DEFAULT 0;
ALTER TABLE `settings` ADD `enableVRF` tinyint(1) DEFAULT '1';

/* add mac to address table */
ALTER TABLE `ipaddresses` ADD `mac` varchar(20) DEFAULT NULL after `dns_name`;

/* Reset donations */
UPDATE `settings` set `donate` = '0'; 

/* Expand logs table */
ALTER TABLE `logs` ADD `details` varchar(1024) DEFAULT '0';

/* Add version */
UPDATE `settings` set `version` = '0.5';


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
  `sections` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hostname` (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Dump of table vrf
# ------------------------------------------------------------
DROP TABLE IF EXISTS `vrf`;

CREATE TABLE `vrf` (
  `vrfId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `rd` varchar(32) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`vrfId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Dump of table settingsDomain
# ------------------------------------------------------------
DROP TABLE IF EXISTS `settingsDomain`;

CREATE TABLE `settingsDomain` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_suffix` varchar(256) DEFAULT '@domain.local',
  `base_dn` varchar(256) DEFAULT 'CN=Users,CN=Company,DC=domain,DC=local',
  `domain_controllers` varchar(256) DEFAULT 'dc1.domain.local;dc2.domain.local',
  `use_ssl` tinyint(1) DEFAULT '0',
  `use_tls` tinyint(1) DEFAULT '0',
  `ad_port` int(5) DEFAULT '389',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `settingsDomain` (`account_suffix`, `base_dn`, `domain_controllers`, `use_ssl`, `use_tls`, `ad_port` )
values ("@domain.local", "CN=Users,CN=Company,DC=domain,DC=local", "dc1.domain.local;dc2.domain.local", "0", "0", "389");