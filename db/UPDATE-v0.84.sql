/* Update from v 0.83 to 0.84 **/
UPDATE `settings` set `version` = '0.84'; 	/* UPDATE version */
ALTER TABLE `sections` ADD `subnetOrdering` VARCHAR(16)  NULL  DEFAULT NULL  AFTER `strictMode`;	/* per-section subnet ordering */
ALTER TABLE `sections` ADD `order` INT(3)  NULL  DEFAULT NULL  AFTER `subnetOrdering`;				/* section ordering */