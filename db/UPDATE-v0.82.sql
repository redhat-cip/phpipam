/* Update from v 0.81 to 0.82 **/
UPDATE `settings` set `version` = '0.82'; 	/* UPDATE version */
/* Add lastseen field */
ALTER TABLE `ipaddresses` ADD `lastSeen` DATETIME  NULL  DEFAULT '0000-00-00 00:00:00'  AFTER `note`;
/* exclude from cron ping script */
ALTER TABLE `ipaddresses` ADD `excludePing` BINARY  NULL  DEFAULT '0'  AFTER `lastSeen`;
/* Add switch for pinging subnet IP addresses from cron script */
ALTER TABLE `subnets` ADD `pingSubnet` BOOL  NOT NULL  DEFAULT '0'  AFTER `permissions`;
/* add detection intervals to settings for ping check statuses */
ALTER TABLE `settings` ADD `pingStatus` VARCHAR(12)  NOT NULL  DEFAULT '1800;3600'  AFTER `htmlMail`;
