/************************
Update from v 0.4 to 0.5 
************************/


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