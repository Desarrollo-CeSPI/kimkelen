ALTER TABLE `career_student` ADD `status` INTEGER default 1;

CREATE TABLE `course_preceptor`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`preceptor_id` INTEGER  NOT NULL,
	`course_id` INTEGER  NOT NULL,
	PRIMARY KEY (`id`,`preceptor_id`,`course_id`),
	INDEX `course_preceptor_FI_1` (`preceptor_id`),
	CONSTRAINT `course_preceptor_FK_1`
		FOREIGN KEY (`preceptor_id`)
		REFERENCES `sf_guard_user` (`id`)
		ON DELETE CASCADE,
	INDEX `course_preceptor_FI_2` (`course_id`),
	CONSTRAINT `course_preceptor_FK_2`
		FOREIGN KEY (`course_id`)
		REFERENCES `course` (`id`)
		ON DELETE CASCADE
)Type=InnoDB COMMENT='Representa la relación entre los cursos y su/sus preceptores a cargo';

CREATE TABLE `personal_guard_user`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`sf_guard_user_id` INTEGER  NOT NULL,
	`personal_id` INTEGER  NOT NULL,
	PRIMARY KEY (`id`,`sf_guard_user_id`,`personal_id`),
	UNIQUE KEY `teacher_guard_user` (`sf_guard_user_id`),
	CONSTRAINT `personal_guard_user_FK_1`
		FOREIGN KEY (`sf_guard_user_id`)
		REFERENCES `sf_guard_user` (`id`)
		ON DELETE CASCADE,
	INDEX `personal_guard_user_FI_2` (`personal_id`),
	CONSTRAINT `personal_guard_user_FK_2`
		FOREIGN KEY (`personal_id`)
		REFERENCES `personal` (`id`)
		ON DELETE RESTRICT
)Type=InnoDB COMMENT='Representa la relación entre un preceptor y su usuario en el sistema';
/* old definition: int(11) NOT NULL COMMENT 'Carga horaria de la materia'
   new definition: INTEGER COMMENT 'Carga horaria de la materia' */
ALTER TABLE `career_subject` CHANGE `credit_hours` `credit_hours` INTEGER COMMENT 'Carga horaria de la materia';
/* old definition: decimal(10,0) DEFAULT NULL COMMENT 'Nota que obtuvo el alumno'
   new definition: DECIMAL COMMENT 'Nota que obtuvo el alumno' */
ALTER TABLE `course_student_mark` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota que obtuvo el alumno';
/* old definition: decimal(10,0) DEFAULT NULL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.'
   new definition: DECIMAL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.' */
ALTER TABLE `examination_inscription` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.';
