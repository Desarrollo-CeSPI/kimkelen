ALTER TABLE  `person` CHANGE  `birth_city`  `birth_city` INT NULL DEFAULT NULL ;

ALTER TABLE `person`
  DROP `birth_state`,
  DROP `birth_country`;

CREATE TABLE `department`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255)  NOT NULL,
	`state_id` INTEGER  NOT NULL,
	`status` VARCHAR(1),
	PRIMARY KEY (`id`),
	INDEX `department_FI_1` (`state_id`)
)Engine=InnoDB COMMENT='Representa un partido o departamento';


ALTER TABLE `address` DROP FOREIGN KEY `address_FK_1`;
ALTER TABLE `address` DROP FOREIGN KEY `address_FK_2`;
ALTER TABLE `city` DROP FOREIGN KEY `city_FK_1`;

ALTER TABLE `city` ADD `short_name` VARCHAR(255)  NOT NULL, ADD `department_id` INTEGER, ADD  INDEX `city_FI_1` (`department_id`), ADD CONSTRAINT `city_FK_1`
		FOREIGN KEY (`department_id`)
		REFERENCES `department` (`id`), DROP INDEX state, DROP `zip_code`, DROP `state_id`;
ALTER TABLE `state` ADD  INDEX `state_FI_1` (`country_id`), DROP INDEX country;

ALTER TABLE `address` ADD CONSTRAINT `address_FK_1`
		FOREIGN KEY (`city_id`)
		REFERENCES `city` (`id`), DROP INDEX address_FI_1, ADD  INDEX `address_FI_1` (`city_id`), DROP INDEX address_FI_2, DROP `state_id`;


ALTER TABLE  `address` ADD  `old_city_id` INT NULL DEFAULT NULL;

ALTER TABLE  `person` ADD  `old_birth_city` INT NULL DEFAULT NULL;