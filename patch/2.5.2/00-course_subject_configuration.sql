CREATE TABLE `course_subject_configuration`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`course_subject_id` INTEGER  NOT NULL,
	`career_school_year_period_id` INTEGER COMMENT 'Referencia a al periodo padre (En caso de ser un bimestre, señala a un cuatrimestre padre).',
	`max_absence` FLOAT COMMENT 'Define la cantidad de faltas permitidas en un periodo',
	PRIMARY KEY (`id`),
	INDEX `course_subject_configuration_FI_1` (`course_subject_id`),
	CONSTRAINT `course_subject_configuration_FK_1`
		FOREIGN KEY (`course_subject_id`)
		REFERENCES `course_subject` (`id`)
		ON DELETE CASCADE,
	INDEX `course_subject_configuration_FI_2` (`career_school_year_period_id`),
	CONSTRAINT `course_subject_configuration_FK_2`
		FOREIGN KEY (`career_school_year_period_id`)
		REFERENCES `career_school_year_period` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE
)Type=InnoDB COMMENT='Representa la configuracion de un curso';
DROP TABLE `absence_per_day`;
DROP TABLE `absence_per_subject`;
DROP TABLE `absence_reason`;
DROP TABLE `disciplinary_sanction`;
DROP TABLE `justification`;
