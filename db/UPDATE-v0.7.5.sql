/*******************************************
Update from v 0.7 to 0.75 - adds permissions
*******************************************/


/* UPDATE version */
UPDATE `settings` set `version` = '0.8';
UPDATE `settings` set `donate` = '0';


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


/* Add strictMode per section */
ALTER TABLE `sections` ADD `strictMode` INT(1)  NOT NULL  DEFAULT '1';

/* remove strictMode form settings */
ALTER TABLE `settings` DROP `strictMode`;