ALTER TABLE `career_student` ADD `graduation_school_year_id` INTEGER default null, ADD  INDEX `career_student_FI_5` (`graduation_school_year_id`), ADD CONSTRAINT `career_student_FK_5`
                FOREIGN KEY (`graduation_school_year_id`)
                REFERENCES `school_year` (`id`);
ALTER TABLE `examination_subject` ADD UNIQUE INDEX `examination_subject_unique` (`examination_id`, `career_subject_school_year_id`), DROP INDEX examination_subject_FI_1;