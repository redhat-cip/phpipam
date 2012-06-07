/************************
Update from v 0.6 to 0.7 
************************/

/* UPDATE version */
UPDATE `settings` set `version` = '0.7';
UPDATE `settings` set `donate` = '0';

/* add show names */
ALTER TABLE `subnets` ADD `showName` tinyint(1) DEFAULT '0';