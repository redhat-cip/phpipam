/************************
Update from v 0.7 to 0.8 
************************/

/* UPDATE version */
UPDATE `settings` set `version` = '0.8';
UPDATE `settings` set `donate` = '0';

/* remove useFullPageWidth */
ALTER TABLE `users` DROP `useFullPageWidth`;