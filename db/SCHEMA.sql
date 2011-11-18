/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table settings
# ------------------------------------------------------------
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

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;

INSERT INTO `settings` (`id`, `siteTitle`, `siteAdminName`, `siteAdminMail`, `siteDomain`, `siteURL`, `domainAuth`, `showTooltips`, `enableIPrequests`, `enableDNSresolving`, `version`)
VALUES
	(1,'phpipam IP address management (v0.4)','Sysadmin','admin@domain.local','domain.local','yourpublicurl.com',0,1,1,0, '0.4');

/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `password` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `role` text CHARACTER SET utf8,
  `real_name` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `domainUser` binary(1) DEFAULT '0',
  PRIMARY KEY (`username`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `username`, `password`, `role`, `real_name`, `email`, `domainUser`)
VALUES
	(2,'Admin',X'6431306262383036653937643335333866623133623535383164623131653965','Administrator','Admin user','admin@domain.local',X'30');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sections
# ------------------------------------------------------------
DROP TABLE IF EXISTS `sections`;

CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `description` text,
  PRIMARY KEY (`name`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;

INSERT INTO `sections` (`id`, `name`, `description`)
VALUES
	(1,'Core Network','Core Network IP addresses'),
	(2,'Servers','IP addresses for production servers'),
	(3,'IPv6','IPv6 IP addresses');

/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table subnets
# ------------------------------------------------------------
DROP TABLE IF EXISTS `subnets`;

CREATE TABLE `subnets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subnet` varchar(255) NOT NULL,
  `mask` varchar(255) NOT NULL,
  `sectionId` varchar(20) DEFAULT NULL,
  `description` text NOT NULL,
  `VLAN` int(255) NOT NULL,
  `masterSubnetId` varchar(32) DEFAULT NULL,
  `allowRequests` tinyint(1) DEFAULT '0',
  `adminLock` binary(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

LOCK TABLES `subnets` WRITE;
/*!40000 ALTER TABLE `subnets` DISABLE KEYS */;

INSERT INTO `subnets` (`id`, `subnet`, `mask`, `sectionId`, `description`, `VLAN`, `masterSubnetId`)
VALUES
	(4,'168427520','16','1','Master subnet 1',1000,'0'),
	(2,'1507077376','24','2','Public server section',1200,NULL),
	(3,'42540579432819059193067264187931033600','32','3','IPv6 subnet 1',1300,NULL),
	(5,'168427776','24','1','Subnet 1',1010,'4'),
	(6,'168428032','24','1','Subnet 2',1020,'4'),
	(7,'184483840','24','1','Subnet 255',1500,'0');

/*!40000 ALTER TABLE `subnets` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ipaddresses
# ------------------------------------------------------------
DROP TABLE IF EXISTS `ipaddresses`;

CREATE TABLE `ipaddresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subnetId` text NOT NULL,
  `ip_addr` varchar(100) NOT NULL,
  `description` varchar(64) DEFAULT NULL,
  `dns_name` varchar(32) NOT NULL,
  `owner` varchar(32) DEFAULT NULL,
  `state` varchar(1) DEFAULT '1',
  `switch` varchar(32) DEFAULT NULL,
  `port` varchar(32) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

LOCK TABLES `ipaddresses` WRITE;
/*!40000 ALTER TABLE `ipaddresses` DISABLE KEYS */;

INSERT INTO `ipaddresses` (`id`, `subnetId`, `ip_addr`, `description`, `dns_name`, `owner`, `state`, `switch`, `port`, `note`)
VALUES
	(8,'6','168428042','Host 10','host10.sub2.local','Admin','1','Switch01','Ge 2/0',''),
	(5,'2','1507077387','Server 1','server1.mydomain.local','Admin','1','','Ge1/34',NULL),
	(7,'5','168427777','Gateway','gw.sub1.local','Admin','1','Switch01','ge 0/1','Gateway for Subnet 1'),
	(6,'3','42540579432819059193067264187931033601','IPv6 Gateway','gw.ipv6.local','Admin','1','Switch01','ge 1/1','IPv6 Gateway');

/*!40000 ALTER TABLE `ipaddresses` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table instructions
# ------------------------------------------------------------
DROP TABLE IF EXISTS `instructions`;

CREATE TABLE `instructions` (
  `id` int(11) NOT NULL,
  `instructions` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `instructions` WRITE;
/*!40000 ALTER TABLE `instructions` DISABLE KEYS */;

INSERT INTO `instructions` (`id`, `instructions`)
VALUES
	(1,'You can write instructions under admin menu!');

/*!40000 ALTER TABLE `instructions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table logs
# ------------------------------------------------------------
DROP TABLE IF EXISTS `logs`;

CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `severity` int(11) DEFAULT NULL,
  `date` varchar(32) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `command` varchar(128) DEFAULT '0',
  `details` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Dump of table requests
# ------------------------------------------------------------
DROP TABLE IF EXISTS `requests`;

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


# Dump of table requests
# ------------------------------------------------------------
DROP TABLE IF EXISTS `switches`;

CREATE TABLE `switches` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hostname` varchar(32) DEFAULT NULL,
  `ip_addr` varchar(100) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `notes` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hostname` (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
