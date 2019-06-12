CREATE TABLE `log_close_career_school_year` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NULL,
  `course_subject_student_id` INT NULL,
  `course_result` VARCHAR(60) NULL,
  `course_result_id` INT NULL,
  `username` VARCHAR(60) NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `log_close_career_school_year` 
ADD INDEX `index2` (`course_subject_student_id` ASC);

ALTER TABLE `log_close_career_school_year` 
ADD CONSTRAINT `fk_log_close_career_school_year_1`
  FOREIGN KEY (`course_subject_student_id`)
  REFERENCES `course_subject_student` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
