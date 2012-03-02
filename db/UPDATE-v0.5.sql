/************************
Update from v 0.5 to 0.6 
************************/

/* UPDATE version */
UPDATE `settings` set `version` = '0.6';
UPDATE `settings` set `donate` = '0';

/* Add IPfilter to settings */
ALTER TABLE `settings` ADD `IPfilter` varchar(128) DEFAULT NULL;
UPDATE `settings` set `IPfilter` = 'mac;owner;state;switch;port;note';

/* ALTER subnets - add VLAN support */
ALTER TABLE `subnets` ADD COLUMN `vlanId` INTEGER(11) DEFAULT NULL;


# Dump of table VLANS
# ------------------------------------------------------------
CREATE TABLE `vlans` (
    `vlanId` INTEGER(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `number` INTEGER(4),
    `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
    PRIMARY KEY (`vlanId`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;