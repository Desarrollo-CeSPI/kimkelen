ALTER TABLE `address` 
ADD COLUMN `postal_code` VARCHAR(45) NULL AFTER `flat`;
ALTER TABLE `person` 
ADD COLUMN `alternative_phone` VARCHAR(255) NULL AFTER `phone`;

ALTER TABLE `student` 
ADD COLUMN `photos_authorization` TINYINT(4) NULL DEFAULT 0 AFTER `judicial_restriction`,
ADD COLUMN `withdrawal_authorization` TINYINT(4) NULL DEFAULT 0 AFTER `photos_authorization`;

CREATE TABLE `family_relationship` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC));

CREATE TABLE `authorized_person` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `person_id` INT NULL,
  `family_relationship_id` INT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `authorized_person` 
ADD INDEX `index2` (`person_id` ASC);

ALTER TABLE `authorized_person` 
ADD INDEX `index3` (`family_relationship_id` ASC);

ALTER TABLE `authorized_person` 
ADD CONSTRAINT `fk_authorized_person_1`
  FOREIGN KEY (`person_id`)
  REFERENCES `person` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `authorized_person` 
ADD CONSTRAINT `fk_authorized_person_2`
  FOREIGN KEY (`family_relationship_id`)
  REFERENCES `family_relationship` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

CREATE TABLE `student_authorized_person` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `authorized_person_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `student_authorized_person` 
ADD INDEX `index2` (`authorized_person_id` ASC);

ALTER TABLE `student_authorized_person` 
ADD INDEX `index3` (`student_id` ASC);

ALTER TABLE `student_authorized_person` 
ADD CONSTRAINT `fk_student_authorized_person_1`
  FOREIGN KEY (`authorized_person_id`)
  REFERENCES `authorized_person` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `student_authorized_person` 
ADD CONSTRAINT `fk_student_authorized_person_2`
  FOREIGN KEY (`student_id`)
  REFERENCES `student` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


insert into sf_guard_permission (name, description) values ('show_authorized_person' , 'Listar y ver detalle de personas autorizadas a retirar al alumno');
insert into sf_guard_permission (name, description) values ('edit_authorized_person' , 'Editar personas autorizadas a retirar al alumno');
insert into sf_guard_permission (name, description) values ('delete_authorized_person' , 'Eliminar personas autorizadas a retirar al alumno');
insert into sf_guard_permission (name, description) values ('print_student_card' , 'Imprimir planilla personal del alumno');


