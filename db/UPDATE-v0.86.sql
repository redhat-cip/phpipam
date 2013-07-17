/* Update from v 0.85 to 0.86 **/
UPDATE `settings` set `version` = '0.86'; 	/* UPDATE version */
/* add dhcpCompress field */
ALTER TABLE `settings` ADD `dhcpCompress` BOOL  NOT NULL  DEFAULT '0' AFTER `editDate`;
/* add isFolder to subnets */
ALTER TABLE `subnets` ADD `isFolder` INT  NOT NULL  DEFAULT '0'  AFTER `pingSubnet`;
