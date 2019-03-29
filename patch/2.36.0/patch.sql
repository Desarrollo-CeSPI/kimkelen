insert into sf_guard_permission (name, description) values ('edit_medical_certificate','Crear, editar certificados médicos');
insert into sf_guard_permission (name, description) values ('show_medical_certificate','Listar y ver detalles de certificados médicos');

CREATE TABLE `medical_certificate` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(100) NULL,
  `certificate` VARCHAR(255) NULL,
  `school_year_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `certificate_status_id` INT NULL,
  `date` DATETIME NULL,
  `theoric_class` TINYINT NULL,
  `theoric_class_from` DATETIME NULL,
  `theoric_class_to` DATETIME NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `medical_certificate` 
ADD INDEX `index2` (`school_year_id` ASC);

ALTER TABLE `medical_certificate` 
ADD INDEX `index3` (`student_id` ASC);

ALTER TABLE `medical_certificate` 
ADD CONSTRAINT `fk_medical_certificate_1`
  FOREIGN KEY (`school_year_id`)
  REFERENCES `school_year` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_medical_certificate_2`
  FOREIGN KEY (`student_id`)
  REFERENCES `student` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

CREATE TABLE `log_medical_certificate` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `updated_at` DATETIME NULL,
  `medical_certificate_id` INT NOT NULL,
  `username` VARCHAR(60) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `certificate` VARCHAR(100) NULL,
  `school_year_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `certificate_status_id` INT NULL,
  `date` DATETIME NULL,
  `theoric_class` TINYINT NULL,
  `theoric_class_from` DATETIME NULL,
  `theoric_class_to` DATETIME NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `log_medical_certificate` 
ADD INDEX `index2` (`student_id` ASC);
ALTER TABLE `log_medical_certificate` 
ADD INDEX `index3` (`school_year_id` ASC);
ALTER TABLE `log_medical_certificate` 
ADD INDEX `index4` (`medical_certificate_id` ASC);

ALTER TABLE `log_medical_certificate` 
ADD CONSTRAINT `fk_log_medical_certificate_1`
  FOREIGN KEY (`medical_certificate_id`)
  REFERENCES `medical_certificate` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_log_medical_certificate_2`
  FOREIGN KEY (`school_year_id`)
  REFERENCES `school_year` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_log_medical_certificate_3`
  FOREIGN KEY (`student_id`)
  REFERENCES `student` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `career_student` 
ADD COLUMN `admission_date` DATETIME NULL DEFAULT NULL AFTER `graduation_school_year_id`;


