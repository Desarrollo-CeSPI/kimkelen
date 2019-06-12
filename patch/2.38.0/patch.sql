CREATE TABLE `nacio_05jun2019`.`book` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `description` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NULL DEFAULT 1
  PRIMARY KEY (`id`));


ALTER TABLE `student_examination_repproved_subject` 
ADD COLUMN `book_id` INT(11) NULL AFTER `folio_number`;


ALTER TABLE `student_examination_repproved_subject` 
ADD INDEX `student_examination_repproved_subject_FI_3` (`book_id` ASC);

ALTER TABLE `student_examination_repproved_subject` 
ADD CONSTRAINT `student_examination_repproved_subject_FK_3`
  FOREIGN KEY (`book_id`)
  REFERENCES `nacio_05jun2019`.`book` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;