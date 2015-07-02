SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `absence_type` ADD `order` INTEGER  NOT NULL, CHANGE `value` `value` DECIMAL(3,2) default 0 NOT NULL;
