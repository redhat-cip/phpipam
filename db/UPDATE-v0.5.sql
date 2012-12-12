/************************
Update from v 0.5 to 0.7 
************************/

/* UPDATE version */
UPDATE `settings` set `version` = '0.7';
UPDATE `settings` set `donate` = '0';

/* Add IPfilter to settings */
ALTER TABLE `settings` ADD `IPfilter` varchar(128) DEFAULT NULL;
UPDATE `settings` set `IPfilter` = 'mac;owner;state;switch;port;note';
/* strict mode */
ALTER TABLE `settings` ADD `strictMode` tinyint(1) DEFAULT '1';
/* add printLimit */
ALTER TABLE `settings` ADD `printLimit` int(4) unsigned DEFAULT '25';
/* add vlan duplicate option */
ALTER TABLE `settings` ADD `vlanDuplicate` int(1) DEFAULT '0';
/* add subnet sorting */
ALTER TABLE `settings` ADD `subnetOrdering` VARCHAR(16)  NULL  DEFAULT 'subnet,asc';

/* add show names */
ALTER TABLE `subnets` ADD `showName` tinyint(1) DEFAULT '0';

/* Add FullWidth theme option to users! */
ALTER TABLE `users` ADD `useFullPageWidth` tinyint(1) DEFAULT '0';

/* ALTER subnets - add VLAN support */
ALTER TABLE `subnets` ADD COLUMN `vlanId` INTEGER(11) DEFAULT NULL;

/* ALTER ipaddresses - expand dns_name to 64 chars */
ALTER TABLE `ipaddresses` CHANGE COLUMN `dns_name` `dns_name` VARCHAR(64) CHARACTER SET utf8 DEFAULT NULL;

/* Add ipaddr to logs */
ALTER TABLE `logs` ADD `ipaddr` VARCHAR(64)  NULL  DEFAULT NULL  AFTER `username`;


# Dump of table VLANS
# ------------------------------------------------------------
CREATE TABLE `vlans` (
    `vlanId` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `number` INTEGER(4),
    `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
    PRIMARY KEY (`vlanId`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;