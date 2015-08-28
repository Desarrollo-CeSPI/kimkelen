CREATE TABLE `pathway`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255),
	`school_year_id` INTEGER  NOT NULL COMMENT 'Referencia el año lectivo',
	PRIMARY KEY (`id`),
	INDEX `pathway_FI_1` (`school_year_id`),
	CONSTRAINT `pathway_FK_1`
		FOREIGN KEY (`school_year_id`)
		REFERENCES `school_year` (`id`)
)Engine=InnoDB COMMENT='Representa una trayectoria';

CREATE TABLE `pathway_student`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`student_id` INTEGER  NOT NULL COMMENT 'Referencia al estudiante',
	`pathway_id` INTEGER  NOT NULL COMMENT 'Referencia a la trayectoria',
	`year` INTEGER COMMENT 'Representa el año para el cual el alumno se inscribe en trayectoria',
	PRIMARY KEY (`id`),
	UNIQUE KEY `pathway_student` (`pathway_id`, `student_id`),
	KEY `pathway_student_index`(`pathway_id`, `student_id`),
	INDEX `pathway_student_FI_1` (`student_id`),
	CONSTRAINT `pathway_student_FK_1`
		FOREIGN KEY (`student_id`)
		REFERENCES `student` (`id`),
	CONSTRAINT `pathway_student_FK_2`
		FOREIGN KEY (`pathway_id`)
		REFERENCES `pathway` (`id`)
)Engine=InnoDB COMMENT='Representa la inscripción de un alumno en una trayectoria';

CREATE TABLE `course_subject_student_pathway`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`student_id` INTEGER  NOT NULL COMMENT 'Referencia al estudiante',
	`course_subject_id` INTEGER  NOT NULL COMMENT 'Referencia a la materia dentro del curso',
	`mark` DECIMAL(5,2) COMMENT 'Representa la nota que obtiene el alumno en la trayectoria. Se aprueba con 7 (CNLP).',
	`approval_date` DATE COMMENT 'Representa la fecha de aprobación del curso trayectoria',
	`pathway_student_id` INTEGER  NOT NULL COMMENT 'Referencia a la trayectoria',
	PRIMARY KEY (`id`),
	INDEX `course_subject_student_pathway_FI_1` (`student_id`),
	CONSTRAINT `course_subject_student_pathway_FK_1`
		FOREIGN KEY (`student_id`)
		REFERENCES `student` (`id`),
	INDEX `course_subject_student_pathway_FI_2` (`course_subject_id`),
	CONSTRAINT `course_subject_student_pathway_FK_2`
		FOREIGN KEY (`course_subject_id`)
		REFERENCES `course_subject` (`id`),
	INDEX `course_subject_student_pathway_FI_3` (`pathway_student_id`),
	CONSTRAINT `course_subject_student_pathway_FK_3`
		FOREIGN KEY (`pathway_student_id`)
		REFERENCES `pathway_student` (`id`)
)Engine=InnoDB COMMENT='Representa la inscripción de un alumno en un curso de trayectoria';

ALTER TABLE `course` ADD `is_pathway` TINYINT default 0;

INSERT INTO sf_guard_permission VALUES (null, 'show_pathway', 'Listar y ver detalle de trayectorias');
INSERT INTO sf_guard_permission VALUES (null, 'edit_pathway', 'Crear, editar y eliminar trayectorias');

CREATE TABLE `tentative_repproved_student`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`student_career_school_year_id` INTEGER  NOT NULL,
	`is_deleted` TINYINT default 0,
	PRIMARY KEY (`id`),
	INDEX `tentative_repproved_student_FI_1` (`student_career_school_year_id`),
	CONSTRAINT `tentative_repproved_student_FK_1`
		FOREIGN KEY (`student_career_school_year_id`)
		REFERENCES `student_career_school_year` (`id`)
)Engine=InnoDB COMMENT='Representa un alumno que puede llegar a repetir o a ser marcado como trayectoria';