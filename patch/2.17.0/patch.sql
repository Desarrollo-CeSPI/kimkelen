ALTER TABLE `course_subject_student` ADD `is_not_averageable` TINYINT default 0 COMMENT 'El alumno no será calificado numéricamente en este curso';

SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `course_subject_student_mark` ADD FOREIGN KEY (`course_subject_student_id`) REFERENCES `course_subject_student`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;