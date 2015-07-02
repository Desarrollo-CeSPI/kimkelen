SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `career_school_year_period` DROP FOREIGN KEY `career_school_year_period_FK_2`;
ALTER TABLE `course_subject` DROP FOREIGN KEY `course_subject_FK_2`;
ALTER TABLE `division` DROP FOREIGN KEY `division_FK_2`;
ALTER TABLE `student_approved_career_subject` DROP FOREIGN KEY `student_approved_career_subject_FK_1`;
ALTER TABLE `student_approved_career_subject` DROP FOREIGN KEY `student_approved_career_subject_FK_2`;
ALTER TABLE `student_approved_career_subject` DROP FOREIGN KEY `student_approved_career_subject_FK_3`;
ALTER TABLE `student_approved_course_subject` DROP FOREIGN KEY `student_approved_course_subject_FK_1`;
ALTER TABLE `student_approved_course_subject` DROP FOREIGN KEY `student_approved_course_subject_FK_2`;
ALTER TABLE `student_approved_course_subject` DROP FOREIGN KEY `student_approved_course_subject_FK_3`;
ALTER TABLE `student_approved_course_subject` DROP FOREIGN KEY `student_approved_course_subject_FK_4`;
ALTER TABLE `student_career_school_year` DROP FOREIGN KEY `student_career_school_year_FK_1`;
ALTER TABLE `student_disapproved_course_subject` DROP FOREIGN KEY `student_disapproved_course_subject_FK_1`;
ALTER TABLE `student_disapproved_course_subject` DROP FOREIGN KEY `student_disapproved_course_subject_FK_2`;
ALTER TABLE `absence_type` ADD `order` INTEGER  NOT NULL, CHANGE `value` `value` DECIMAL(3,2) default 0 NOT NULL;
ALTER TABLE `student_approved_career_subject` ADD  INDEX `student_id` (`student_id`), ADD  INDEX `career_subject` (`career_subject_id`), ADD CONSTRAINT `student_approved_career_subject_FK_1`
		FOREIGN KEY (`career_subject_id`)
		REFERENCES `career_subject` (`id`)
		ON DELETE RESTRICT, ADD CONSTRAINT `student_approved_career_subject_FK_2`
		FOREIGN KEY (`student_id`)
		REFERENCES `student` (`id`)
		ON DELETE RESTRICT, ADD CONSTRAINT `student_approved_career_subject_FK_3`
		FOREIGN KEY (`school_year_id`)
		REFERENCES `school_year` (`id`)
		ON DELETE RESTRICT, DROP INDEX student_approved_career_subject_FI_2;
ALTER TABLE `student_approved_course_subject` ADD  INDEX `student` (`student_id`), ADD  INDEX `course_subject` (`course_subject_id`), ADD CONSTRAINT `student_approved_course_subject_FK_1`
		FOREIGN KEY (`course_subject_id`)
		REFERENCES `course_subject` (`id`)
		ON DELETE RESTRICT, ADD CONSTRAINT `student_approved_course_subject_FK_2`
		FOREIGN KEY (`student_id`)
		REFERENCES `student` (`id`)
		ON DELETE RESTRICT, ADD CONSTRAINT `student_approved_course_subject_FK_3`
		FOREIGN KEY (`school_year_id`)
		REFERENCES `school_year` (`id`)
		ON DELETE RESTRICT, ADD CONSTRAINT `student_approved_course_subject_FK_4`
		FOREIGN KEY (`student_approved_career_subject_id`)
		REFERENCES `student_approved_career_subject` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE;
ALTER TABLE `student_disapproved_course_subject` ADD  INDEX `course_subject` (`course_subject_student_id`), ADD  INDEX `student_approved_career_subject` (`student_approved_career_subject_id`), ADD CONSTRAINT `student_disapproved_course_subject_FK_1`
		FOREIGN KEY (`course_subject_student_id`)
		REFERENCES `course_subject_student` (`id`)
		ON DELETE RESTRICT, ADD CONSTRAINT `student_disapproved_course_subject_FK_2`
		FOREIGN KEY (`student_approved_career_subject_id`)
		REFERENCES `student_approved_career_subject` (`id`)
		ON DELETE RESTRICT, DROP INDEX student_disapproved_course_subject_FI_1, DROP INDEX student_disapproved_course_subject_FI_2;
ALTER TABLE `student_career_school_year` ADD `is_processed` TINYINT default 0, ADD CONSTRAINT `student_career_school_year_FK_1`
		FOREIGN KEY (`career_school_year_id`)
		REFERENCES `career_school_year` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE;
ALTER TABLE `sanction_type` ADD `considered_in_report_card` TINYINT default 1;
ALTER TABLE `study` ADD UNIQUE INDEX `study_U_1` (`name`);
ALTER TABLE `career_school_year_period` ADD CONSTRAINT `career_school_year_period_FK_2`
		FOREIGN KEY (`career_school_year_period_id`)
		REFERENCES `career_school_year_period` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE;
ALTER TABLE `course_subject` ADD CONSTRAINT `course_subject_FK_2`
		FOREIGN KEY (`career_subject_school_year_id`)
		REFERENCES `career_subject_school_year` (`id`)
		ON DELETE CASCADE;
ALTER TABLE `division` ADD CONSTRAINT `division_FK_2`
		FOREIGN KEY (`career_school_year_id`)
		REFERENCES `career_school_year` (`id`)
		ON DELETE CASCADE;
ALTER TABLE `student_advice` CHANGE `value` `value` DECIMAL default 0;
ALTER TABLE `student_attendance` CHANGE `value` `value` DECIMAL(3,2) default 0;
ALTER TABLE `tutor_type` DROP INDEX name;
update student_career_school_year set is_processed = 1 where career_school_year_id = 2;

