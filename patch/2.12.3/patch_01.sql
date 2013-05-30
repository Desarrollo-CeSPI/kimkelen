SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `career_school_year_period` DROP FOREIGN KEY `career_school_year_period_FK_2`;
ALTER TABLE `course_subject` DROP FOREIGN KEY `course_subject_FK_2`;
ALTER TABLE `division` DROP FOREIGN KEY `division_FK_2`;
ALTER TABLE `student_approved_course_subject` DROP FOREIGN KEY `student_approved_course_subject_FK_4`;
ALTER TABLE `student_career_school_year` DROP FOREIGN KEY `student_career_school_year_FK_1`;
ALTER TABLE `study` ADD UNIQUE INDEX `study_U_1` (`name`);
ALTER TABLE `absence_type` CHANGE `value` `value` DECIMAL(3,2) default 0 NOT NULL;
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
ALTER TABLE `student_approved_course_subject` ADD CONSTRAINT `student_approved_course_subject_FK_4`
		FOREIGN KEY (`student_approved_career_subject_id`)
		REFERENCES `student_approved_career_subject` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE;
ALTER TABLE `student_attendance` CHANGE `value` `value` DECIMAL(3,2) default 0;
ALTER TABLE `student_career_school_year` ADD CONSTRAINT `student_career_school_year_FK_1`
		FOREIGN KEY (`career_school_year_id`)
		REFERENCES `career_school_year` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE;
ALTER TABLE `tutor_type` DROP INDEX name;
