/************************
Update from v 0.7 to 0.8 
************************/

/* UPDATE version */
UPDATE `settings` set `version` = '0.8';
UPDATE `settings` set `donate` = '0';

/* remove useFullPageWidth */
ALTER TABLE `users` DROP `useFullPageWidth`;

/* add visual Limit */
ALTER TABLE `settings` ADD `visualLimit` INT(2)  NOT NULL  DEFAULT '0';

/* add plain text emails */
ALTER TABLE `settings` ADD `htmlMail` BINARY(1)  NOT NULL  DEFAULT '1';
