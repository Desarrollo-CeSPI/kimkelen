CREATE TABLE `observation_mark` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `letter` VARCHAR(2) NOT NULL,
  `description` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `course_subject_student_mark` 
ADD COLUMN `observation_mark_id` INT NULL;

ALTER TABLE `course_subject_student_mark` 
ADD INDEX `index4` (`observation_mark_id` ASC);

ALTER TABLE `course_subject_student_mark` 
ADD CONSTRAINT `fk_course_subject_student_mark_4`
  FOREIGN KEY (`observation_mark_id`)
  REFERENCES `observation_mark` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
