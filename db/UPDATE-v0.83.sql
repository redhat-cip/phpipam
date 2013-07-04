/* Update from v 0.82 to 0.83 **/
UPDATE `settings` set `version` = '0.83'; 	/* UPDATE version */
/* Add defaultLang field */
ALTER TABLE `settings` ADD `defaultLang` INT(3)  NULL  DEFAULT NULL  AFTER `pingStatus`;
/* Add French language */
INSERT INTO `lang` (`l_code`,`l_name`) values ("fr_FR", "Français");