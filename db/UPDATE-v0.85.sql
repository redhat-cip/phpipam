/* Update from v 0.84 to 0.85 **/
UPDATE `settings` set `version` = '0.85'; 	/* UPDATE version */
/* last edited timestamps */
ALTER TABLE `ipaddresses` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
ALTER TABLE `subnets` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
ALTER TABLE `sections` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
ALTER TABLE `vlans` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
ALTER TABLE `vrf` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
ALTER TABLE `users` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
ALTER TABLE `userGroups` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
ALTER TABLE `switches` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
ALTER TABLE `settings` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
ALTER TABLE `settingsDomain` ADD `editDate` TIMESTAMP  NULL  ON UPDATE CURRENT_TIMESTAMP; /* add timestamp to last edited */
/* dutch lanuguage */
INSERT into `lang` (`l_code`,`l_name`) VALUES ('nl_NL','Nederlands');
/* api switch on settings */
ALTER TABLE `settings` ADD `api` BINARY  NOT NULL  DEFAULT '0'  AFTER `defaultLang`;
/* api table */
CREATE TABLE `api` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(32) NOT NULL DEFAULT '',
  `app_code` varchar(32) NOT NULL DEFAULT '',
  `app_permissions` int(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_id` (`app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;