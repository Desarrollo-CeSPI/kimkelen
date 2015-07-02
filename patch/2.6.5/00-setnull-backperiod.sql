SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `course_subject_student` DROP FOREIGN KEY `course_subject_student_FK_3`;
ALTER TABLE `course_subject_student` ADD CONSTRAINT `course_subject_student_FK_3`
		FOREIGN KEY (`student_approved_course_subject_id`)
		REFERENCES `student_approved_course_subject` (`id`)
		ON UPDATE CASCADE
		ON DELETE SET NULL;

