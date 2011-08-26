/************************
Update from v 0.2 to 0.4 
************************/

/* Add note field to ipaddresses table */
ALTER TABLE `ipaddresses` ADD `note` text;

/* Add masterSubnetId, allowRequests and adminLock fields to subnets table */
ALTER TABLE `subnets` ADD `masterSubnetId` varchar(32) DEFAULT NULL;
ALTER TABLE `subnets` ADD `allowRequests` tinyint(1) DEFAULT '0';
ALTER TABLE `subnets` ADD `adminLock` binary(1) DEFAULT '0';

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;


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
  `enableDNSresolving` tinyint(1) DEFAULT NULL,
  `version` varchar(4) DEFAULT NULL,
  `donate` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/* populate default requests */
LOCK TABLES `settings` WRITE;
INSERT INTO `settings` (`id`, `siteTitle`, `siteAdminName`, `siteAdminMail`, `siteDomain`, `siteURL`, `domainAuth`, `showTooltips`, `enableIPrequests`, `enableDNSresolving`, `version`)
VALUES
	(1,'phpipam IP address management (v0.4)','Sysadmin','admin@domain.local','domain.local','yourpublicurl.com',0,1,1,0, '0.4');
UNLOCK TABLES;