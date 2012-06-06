/************************
Update from v 0.6 to 0.7 
************************/

/* UPDATE version */
UPDATE `settings` set `version` = '0.7';
UPDATE `settings` set `donate` = '0';

/* add show names */
ALTER TABLE `settings` ADD `masterNames` tinyint(1) DEFAULT '0';
ALTER TABLE `settings` ADD `slaveNames` tinyint(1) DEFAULT '0';