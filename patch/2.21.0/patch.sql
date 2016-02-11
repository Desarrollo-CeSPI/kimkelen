CREATE TABLE `origin_school`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255)  NOT NULL,
	`email` VARCHAR(80)  NOT NULL,
	`phone` VARCHAR(25)  NOT NULL,
	`cue` INTEGER  NOT NULL,
	`sector` INTEGER  NOT NULL,
	`field` INTEGER  NOT NULL,
	`address` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `cue`(`cue`)
)Engine=InnoDB COMMENT='Representa una escuela primaria de la que provienen los alumnos';

ALTER TABLE `student` ADD `origin_school_id` INTEGER default null, ADD  INDEX `student_FI_4` (`origin_school_id`), ADD CONSTRAINT `student_FK_4`
		FOREIGN KEY (`origin_school_id`)
		REFERENCES `origin_school` (`id`)
		ON DELETE RESTRICT, DROP `origin_school`;