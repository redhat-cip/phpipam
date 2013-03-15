/************************
Update from v 0.6 to 0.8 
************************/

/* UPDATE version */
UPDATE `settings` set `version` = '0.8';
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

/* Add devicetype to switches */
ALTER TABLE `switches` ADD `type` INT(2)  NULL  DEFAULT '0' AFTER `ip_addr`;

/* remove useFullPageWidth */
ALTER TABLE `users` DROP `useFullPageWidth`;

/* add visual Limit */
ALTER TABLE `settings` ADD `visualLimit` INT(2)  NOT NULL  DEFAULT '0';

/* add plain text emails */
ALTER TABLE `settings` ADD `htmlMail` BINARY(1)  NOT NULL  DEFAULT '1';

/* change masterSubnetid to int */
ALTER TABLE `subnets` CHANGE `masterSubnetId` `masterSubnetId` INT(12)  NULL  DEFAULT NULL;

/* change sectionId  to int */
ALTER TABLE `subnets` CHANGE `sectionId` `sectionId` INT(12)  NULL  DEFAULT NULL;



/**
 * User permissions
 */

/* create new table userGroups */
CREATE TABLE `userGroups` (
  `g_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `g_name` varchar(32) DEFAULT NULL,
  `g_desc` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* insert default users */
INSERT INTO `userGroups` (`g_id`, `g_name`, `g_desc`)
VALUES
	(2,'Operators','default Operator group'),
	(3,'Guests','default Guest group (viewers)');

/* add new field groupId to user table */
ALTER TABLE `users` ADD `groups` VARCHAR(1024) NULL DEFAULT NULL AFTER `password`;

/* add users to default groups */
Update `users` set `groups`='' where `role` = "Administrator";
Update `users` set `groups`='{"2":"2"}' where `role` = "Operator";
Update `users` set `groups`='{"3":"3"}' where `role` = "Viewer";

/* Change all non-admin users to default users */
Update `users` set `role`="User" where `role` != "Administrator";

/* add permissions to section table */
ALTER TABLE `sections` ADD `permissions` VARCHAR(1024)  NULL  DEFAULT NULL;
/* insert default permissions for sections */
update `sections` set `permissions` = '{"2":"2","3":"1"}';

/* add permissions to subnets table */
ALTER TABLE `subnets` ADD `permissions` VARCHAR(1024)  NULL  DEFAULT NULL  AFTER `showName`;
/* insert default permissions for subnets */
update `subnets` set `permissions` = '{"2":"1","3":"1"}' where `adminLock` = 1;
update `subnets` set `permissions` = '{"2":"2","3":"1"}' where `adminLock` != 1;
/* remove lock */
ALTER TABLE `subnets` DROP `adminLock`;