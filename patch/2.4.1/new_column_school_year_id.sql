ALTER TABLE `student_advice` ADD `school_year_id` INTEGER  NOT NULL;
ALTER TABLE `student_advice` ADD  INDEX `student_advice_FI_5` (`school_year_id`);
ALTER TABLE `student_advice` ADD CONSTRAINT `student_advice_FK_5`
                FOREIGN KEY (`school_year_id`)
                REFERENCES `school_year` (`id`)
                ON DELETE RESTRICT;
