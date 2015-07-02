SET FOREIGN_KEY_CHECKS=0;
CREATE TABLE `sanction_type`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255)  NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `sanction_type_name` (`name`)
)Engine=InnoDB COMMENT='Representa los tipos de amonestación de alumnos';
ALTER TABLE `student_advice` DROP FOREIGN KEY `student_advice_FK_3`;
ALTER TABLE `student_advice` DROP FOREIGN KEY `student_advice_FK_5`;
ALTER TABLE `subject_configuration` ADD `max_disciplinary_sanctions` INTEGER default 0 COMMENT 'Define la cantidad de sanciones permitidas.', DROP `max_absence`;
ALTER TABLE `school_year` ADD `is_closed` TINYINT default 0 COMMENT 'Representa si esta cerrado el año lectivo o no';
ALTER TABLE `career_subject_school_year` ADD `index_sort` INTEGER default 0 COMMENT 'Numero que ordena a las materias';
ALTER TABLE `student_advice` ADD `number` VARCHAR(11), ADD `name` VARCHAR(255), ADD `sanction_type_id` INTEGER  NOT NULL COMMENT 'Tipo de sancion (apercibimiento, llamdo de atencion, amonestación, ultimo apercibimiento)', ADD  INDEX `student_advice_FI_6` (`school_year_id`), ADD CONSTRAINT `student_advice_FK_6`
		FOREIGN KEY (`school_year_id`)
		REFERENCES `school_year` (`id`)
		ON DELETE RESTRICT, ADD CONSTRAINT `student_advice_FK_3`
		FOREIGN KEY (`sanction_type_id`)
		REFERENCES `sanction_type` (`id`)
		ON DELETE RESTRICT, ADD CONSTRAINT `student_advice_FK_5`
		FOREIGN KEY (`responsible_id`)
		REFERENCES `person` (`id`)
		ON DELETE RESTRICT, DROP INDEX student_advice_FI_3, ADD  INDEX `student_advice_FI_3` (`sanction_type_id`), DROP INDEX student_advice_FI_4, ADD  INDEX `student_advice_FI_4` (`applicant_id`), DROP INDEX student_advice_FI_5, ADD  INDEX `student_advice_FI_5` (`responsible_id`), CHANGE `value` `value` DECIMAL default 0;
ALTER TABLE `course` ADD `related_division_id` INTEGER default null COMMENT 'En caso de que una comision, se quiera relacionar con una division.', ADD  INDEX `course_FI_3` (`related_division_id`), ADD CONSTRAINT `course_FK_3`
		FOREIGN KEY (`related_division_id`)
		REFERENCES `division` (`id`)
		ON DELETE SET NULL;
ALTER TABLE `student` ADD `order_of_merit` VARCHAR(20) COMMENT 'Número de orden de merito del alumno', ADD `folio_number` VARCHAR(20) COMMENT 'Número de folio del alumno';
ALTER TABLE `course_subject_student_examination` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota que obtuvo el alumno';
ALTER TABLE `course_subject_student_mark` CHANGE `mark` `mark` DECIMAL(5,2) COMMENT 'Nota que obtuvo el alumno';
ALTER TABLE `final_examination_subject_student` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota que obtuvo el alumno';
ALTER TABLE `student_attendance` DROP INDEX student_attendance_FI_2, CHANGE `attendance_type` `attendance_type` INTEGER  NOT NULL COMMENT 'Define si una asistencia es por dia o por materia.';
ALTER TABLE `student_examination_repproved_subject` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota que obtuvo el alumno';
