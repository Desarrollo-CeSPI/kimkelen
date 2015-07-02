/*ALTER TABLE `student` ADD UNIQUE INDEX `student_document_number` (`identification_number`);*/
ALTER TABLE `subject_configuration` ADD `regular_mark` INTEGER default 4 COMMENT 'Nota minima para aprobar sin promocion';
/*ALTER TABLE `teacher` ADD UNIQUE INDEX `teacher_identification_number` (`identification_number`);*/
/* old definition: decimal(10,0) default NULL COMMENT 'Nota que obtuvo el alumno'
   new definition: DECIMAL COMMENT 'Nota que obtuvo el alumno' */
ALTER TABLE `course_student_mark` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota que obtuvo el alumno';
/* old definition: decimal(10,0) default NULL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.'
   new definition: DECIMAL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.' */
ALTER TABLE `examination_inscription` CHANGE `mark` `mark` DECIMAL COMMENT 'Nota obtenida por el alumno en la mesa de examen de la materia relacionada.';
/* old definition: varchar(255) NOT NULL
   new definition: VARCHAR(20)  NOT NULL */
ALTER TABLE `student` CHANGE `identification_number` `identification_number` VARCHAR(20)  NOT NULL;
ALTER TABLE `subject` DROP `is_chalenge_the_subject`;
ALTER TABLE `subject` DROP `is_to_promote`;
/* old definition: varchar(255) NOT NULL
   new definition: VARCHAR(20)  NOT NULL */
ALTER TABLE `teacher` CHANGE `identification_number` `identification_number` VARCHAR(20)  NOT NULL;
