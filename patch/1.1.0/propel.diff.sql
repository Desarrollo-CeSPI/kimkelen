ALTER TABLE `student` ADD `occupation_id` INTEGER default null;
ALTER TABLE `student` ADD  INDEX `student_FI_3` (`shift_id`);
/*
===== no anda =====
ALTER TABLE `student` ADD CONSTRAINT `student_FK_2`
		FOREIGN KEY (`occupation_id`)
		REFERENCES `occupation` (`id`)
		ON DELETE RESTRICT;
===== /no anda =====
*/
ALTER TABLE `tutor` ADD `occupation_id` INTEGER  NOT NULL;
ALTER TABLE `tutor` ADD  INDEX `tutor_FI_3` (`address_id`);
/*
===== no anda =====
ALTER TABLE `tutor` ADD CONSTRAINT `tutor_FK_2`
		FOREIGN KEY (`occupation_id`)
		REFERENCES `occupation` (`id`)
		ON DELETE RESTRICT;
===== /no anda =====
*/
ALTER TABLE `career_subject` ADD `credit_hours` INTEGER  NOT NULL COMMENT 'Carga horaria de la materia';
CREATE TABLE `division`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(256)  NOT NULL,
	`status` INTEGER default 0 NOT NULL,
	`school_year_id` INTEGER  NOT NULL,
	`career_id` INTEGER  NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `division_FI_1` (`school_year_id`),
	CONSTRAINT `division_FK_1`
		FOREIGN KEY (`school_year_id`)
		REFERENCES `school_year` (`id`)
		ON DELETE RESTRICT,
	INDEX `division_FI_2` (`career_id`),
	CONSTRAINT `division_FK_2`
		FOREIGN KEY (`career_id`)
		REFERENCES `career` (`id`)
		ON DELETE RESTRICT
)Type=InnoDB;
CREATE TABLE `division_course`
(
	`division_id` INTEGER  NOT NULL,
	`course_id` INTEGER  NOT NULL,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	INDEX `division_course_FI_1` (`division_id`),
	CONSTRAINT `division_course_FK_1`
		FOREIGN KEY (`division_id`)
		REFERENCES `division` (`id`)
		ON DELETE CASCADE,
	INDEX `division_course_FI_2` (`course_id`),
	CONSTRAINT `division_course_FK_2`
		FOREIGN KEY (`course_id`)
		REFERENCES `course` (`id`)
		ON DELETE CASCADE
)Type=InnoDB;
ALTER TABLE `examination` ADD `is_disapproved` TINYINT default 0 COMMENT 'Si es verdadero, es una mesa de examen para alumnos que desaprovaron una cursada, si es falso es una mesa de final';
CREATE TABLE `occupation`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(256)  NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `occupation_U_1` (`name`)
)Type=InnoDB;
ALTER TABLE `course` DROP FOREIGN KEY `course_FK_2`;
ALTER TABLE `course` DROP INDEX course_FI_2;
ALTER TABLE `course` CHANGE `status` `status` INTEGER default 0 COMMENT 'Representa el estado del curso (NUEVO / ACTIVO / NO ACTIVO)';
ALTER TABLE `course` DROP `subject_configuration_id`;
ALTER TABLE `course_student` DROP FOREIGN KEY `course_student_FK_2`;
ALTER TABLE `course_student` ADD CONSTRAINT `course_student_FK_2`
		FOREIGN KEY (`student_id`)
		REFERENCES `student` (`id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT;
ALTER TABLE `course_student_mark` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota que obtuvo el alumno';
ALTER TABLE `examination_inscription` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.';
ALTER TABLE `student` DROP INDEX student_FI_2,        ADD  INDEX `student_FI_2` (`occupation_id`);
ALTER TABLE `student` DROP `occupation`;
ALTER TABLE `student` CHANGE `file_number` `file_number` VARCHAR(10);
ALTER TABLE `subject` DROP FOREIGN KEY `subject_FK_1`;
ALTER TABLE `subject` DROP INDEX subject_FI_1;
ALTER TABLE `subject` DROP `credit_hours`;
ALTER TABLE `subject` DROP `subject_configuration_id`;
ALTER TABLE `tutor` DROP INDEX tutor_FI_2,        ADD  INDEX `tutor_FI_2` (`occupation_id`);
ALTER TABLE `tutor` DROP `occupation`;
CREATE TABLE `optional_career_subject`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`career_subject_id` INTEGER  NOT NULL,
	`optional_career_subject_id` INTEGER  NOT NULL,
	PRIMARY KEY (`id`,`career_subject_id`,`optional_career_subject_id`),
	INDEX `optional_career_subject_FI_1` (`career_subject_id`),
	CONSTRAINT `optional_career_subject_FK_1`
		FOREIGN KEY (`career_subject_id`)
		REFERENCES `career_subject` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	INDEX `optional_career_subject_FI_2` (`optional_career_subject_id`),
	CONSTRAINT `optional_career_subject_FK_2`
		FOREIGN KEY (`optional_career_subject_id`)
		REFERENCES `career_subject` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE
)Type=InnoDB;
/* old definition: decimal(10,0) DEFAULT NULL COMMENT 'Nota que obtuvo el alumno'
   new definition: DECIMAL COMMENT 'Nota que obtuvo el alumno' */
ALTER TABLE `course_student_mark` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota que obtuvo el alumno';
/* old definition: decimal(10,0) DEFAULT NULL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.'
   new definition: DECIMAL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.' */
ALTER TABLE `examination_inscription` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.';
ALTER TABLE `career_subject` ADD `has_options` TINYINT default 0;
ALTER TABLE `career_subject` ADD `is_option` TINYINT default 0;
