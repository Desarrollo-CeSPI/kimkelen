/* Elimino todo los indices de las tablas qe apuntan a origin_school, city, state */

ALTER TABLE `address` DROP FOREIGN KEY `address_FK_1` ;
ALTER TABLE `address` DROP FOREIGN KEY `address_FK_2` ;

ALTER TABLE `city` DROP FOREIGN KEY `city_FK_1` ;
ALTER TABLE `state` DROP FOREIGN KEY `state_FK_1` ;

ALTER TABLE `student` DROP FOREIGN KEY `student_FK_4` ;

/*agrego las columnas que faltan y elimino las que ya no estan*/

ALTER TABLE `address` ADD `department_id` INT DEFAULT NULL AFTER `state_id` ;
ALTER TABLE `city` DROP `zip_code` ;
ALTER TABLE `city` ADD `short_name` VARCHAR( 255 ) NOT NULL AFTER `name` ;
ALTER TABLE `city` CHANGE `state_id` `department_id` INT( 11 ) NOT NULL ;
ALTER TABLE `origin_school` ADD `city_id` INT NOT NULL;
ALTER TABLE `person` ADD `birth_department` INT NULL AFTER `birth_state` ;
ALTER TABLE `person` CHANGE `birth_country` `birth_country` INT NULL DEFAULT NULL ;
ALTER TABLE `person` CHANGE `birth_state` `birth_state` INT NULL DEFAULT NULL ;
ALTER TABLE `person` CHANGE `birth_city` `birth_city` INT NULL DEFAULT NULL ;
