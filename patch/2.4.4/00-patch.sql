
CREATE TABLE `conduct`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255)  NOT NULL,
	`short_name` VARCHAR(255)  NOT NULL,
	PRIMARY KEY (`id`)
)Type=InnoDB COMMENT='Representa la conducta (muy buena, buena, regular, mala)';

CREATE TABLE `career_school_year_period`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255),
	`career_school_year_id` INTEGER  NOT NULL COMMENT 'Referencia a una carrera para un año lectivo',
	`start_at` DATE  NOT NULL,
	`end_at` DATE  NOT NULL,
	`is_closed` TINYINT default 0,
	`course_type` INTEGER COMMENT 'Indica el tipo de la materia (anual, anual cuatrimestral, etc)',
	`career_school_year_period_id` INTEGER COMMENT 'Referencia a al periodo padre (En caso de ser un bimestre, señala a un cuatrimestre padre).',
	PRIMARY KEY (`id`),
	INDEX `career_school_year_period_FI_1` (`career_school_year_id`),
	CONSTRAINT `career_school_year_period_FK_1`
		FOREIGN KEY (`career_school_year_id`)
		REFERENCES `career_school_year` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	INDEX `career_school_year_period_FI_2` (`career_school_year_period_id`),
	CONSTRAINT `career_school_year_period_FK_2`
		FOREIGN KEY (`career_school_year_period_id`)
		REFERENCES `career_school_year_period` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE
)Type=InnoDB COMMENT='Cada tupla representa un periodo en una carrera de un año lectivo';


CREATE TABLE `student_career_school_year_conduct`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`student_career_school_year_id` INTEGER  NOT NULL COMMENT 'Referencia a un estudainte en una carrera para un año lectivo',
	`conduct_id` INTEGER  NOT NULL COMMENT 'Referencia a la conducta',
	`career_school_year_period_id` INTEGER  NOT NULL COMMENT 'Referencia a un periodo',
	PRIMARY KEY (`id`),
	UNIQUE KEY `subject_unique` (`student_career_school_year_id`, `career_school_year_period_id`),
	CONSTRAINT `student_career_school_year_conduct_FK_1`
		FOREIGN KEY (`student_career_school_year_id`)
		REFERENCES `student_career_school_year` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	INDEX `student_career_school_year_conduct_FI_2` (`conduct_id`),
	CONSTRAINT `student_career_school_year_conduct_FK_2`
		FOREIGN KEY (`conduct_id`)
		REFERENCES `conduct` (`id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	INDEX `student_career_school_year_conduct_FI_3` (`career_school_year_period_id`),
	CONSTRAINT `student_career_school_year_conduct_FK_3`
		FOREIGN KEY (`career_school_year_period_id`)
		REFERENCES `career_school_year_period` (`id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
)Type=InnoDB COMMENT='Cada tupla representa el comportamiento de un alumno en un periodo';

