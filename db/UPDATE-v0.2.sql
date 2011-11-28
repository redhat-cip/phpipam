/************************
Update from v 0.2 to 0.5 
************************/

/* Add note field to ipaddresses table */
ALTER TABLE `ipaddresses` ADD `note` text;
ALTER TABLE `ipaddresses` ADD `mac` varchar(20) DEFAULT NULL after `dns_name`;

/* Add masterSubnetId, allowRequests and adminLock fields to subnets table */
ALTER TABLE `subnets` ADD `masterSubnetId` varchar(32) DEFAULT NULL;
ALTER TABLE `subnets` ADD `allowRequests` tinyint(1) DEFAULT '0';
ALTER TABLE `subnets` ADD `adminLock` binary(1) DEFAULT '0';
ALTER TABLE `subnets` ADD `vrfId` int(3) DEFAULT NULL after `VLAN`;

/* Add domain auth option field to users table */
ALTER TABLE `users` ADD `domainUser` binary(1) DEFAULT '0';

/* Expand logs table */
ALTER TABLE `logs` ADD `details` varchar(1024) DEFAULT '0';


/* create table requests */
CREATE TABLE `requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subnetId` varchar(11) DEFAULT NULL,
  `ip_addr` varchar(100) DEFAULT NULL,
  `description` varchar(32) DEFAULT NULL,
  `dns_name` varchar(32) DEFAULT NULL,
  `owner` varchar(32) DEFAULT NULL,
  `requester` varchar(32) DEFAULT NULL,
  `comment` text,
  `processed` binary(1) DEFAULT NULL,
  `accepted` binary(1) DEFAULT NULL,
  `adminComment` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


/* create table settings */
DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `siteTitle` varchar(64) DEFAULT NULL,
  `siteAdminName` varchar(64) DEFAULT NULL,
  `siteAdminMail` varchar(64) DEFAULT NULL,
  `siteDomain` varchar(32) DEFAULT NULL,
  `siteURL` varchar(64) DEFAULT NULL,
  `domainAuth` tinyint(1) DEFAULT NULL,
  `showTooltips` tinyint(1) DEFAULT NULL,
  `enableIPrequests` tinyint(1) DEFAULT NULL,
  `enableVRF` tinyint(1) DEFAULT '1',
  `enableDNSresolving` tinyint(1) DEFAULT NULL,
  `version` varchar(4) DEFAULT NULL,
  `donate` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


/* populate default requests */
LOCK TABLES `settings` WRITE;
INSERT INTO `settings` (`id`, `siteTitle`, `siteAdminName`, `siteAdminMail`, `siteDomain`, `siteURL`, `domainAuth`, `showTooltips`, `enableIPrequests`, `enableDNSresolving`, `version`)
VALUES
	(1,'phpipam IP address management (v0.5)','Sysadmin','admin@domain.local','domain.local','yourpublicurl.com',0,1,1,0, '0.5');
UNLOCK TABLES;


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