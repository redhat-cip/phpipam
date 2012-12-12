/************************
Update from v 0.6 to 0.7 
************************/

/* UPDATE version */
UPDATE `settings` set `version` = '0.7';
UPDATE `settings` set `donate` = '0';

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

/* Add ipaddr to logs */
ALTER TABLE `logs` ADD `ipaddr` VARCHAR(64)  NULL  DEFAULT NULL  AFTER `username`;