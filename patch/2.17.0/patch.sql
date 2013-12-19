ALTER TABLE `course_subject_student` ADD `is_not_averageable` TINYINT default 0 COMMENT 'El alumno no será calificado numéricamente en este curso';

SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `course_subject_student_mark` ADD FOREIGN KEY (`course_subject_student_id`) REFERENCES `course_subject_student`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `career_student` ADD `graduation_school_year` INTEGER default null, ADD  INDEX `career_student_FI_5` (`graduation_school_year`), ADD CONSTRAINT `career_student_FK_5`
                FOREIGN KEY (`graduation_school_year`)
                REFERENCES `school_year` (`id`);

CREATE TABLE `holiday`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`day` DATE  NOT NULL,
	`description` VARCHAR(50),
	PRIMARY KEY (`id`)
)Engine=InnoDB COMMENT='Cada tupla representa un día feriado';

INSERT INTO  `alumnos`.`sf_guard_permission` (
`id` ,
`name` ,
`description`
)
VALUES (
NULL ,  'show_holiday',  'Crear, editar y eliminar días feriados'
);


--ALTER TABLE `examination_subject` ADD UNIQUE INDEX `examination_subject_unique` (`examination_id`, `career_subject_school_year_id`), DROP INDEX examination_subject_FI_1;