CREATE TABLE `book` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `description` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`));


CREATE TABLE `setting_parameter` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `description` VARCHAR(100) NULL,
  `value` INT NULL,
  PRIMARY KEY (`id`));

insert into setting_parameter (name,description) values ('renglones_folio_cursada','Cantidad de líneas/renglones que se generará por cada folio en el Acta de Cursadas/Promoción');
insert into setting_parameter (name,description) values ('renglones_folio_examen','Cantidad de líneas/renglones que se generará por cada folio en el Acta de Examen');
insert into setting_parameter (name,description) values ('renglones_folio_trayectoria','Cantidad de líneas/renglones que se generará por cada folio en el Acta de Trayectoria');

insert into sf_guard_permission(name,description) values ('edit_setting_parameter','Editar paramátros de configuración');
insert into sf_guard_permission(name,description) values ('assign_physical_sheet','Asignar tomo/folio a actas.');
insert into sf_guard_permission(name,description) values ('generate_record','Generar actas.');


CREATE TABLE `record` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NOT NULL,
  `record_type` INT NOT NULL,
  `course_result_id` INT NOT NULL,
  `lines` INT NOT NULL,
  `status` INT NOT NULL,
 `username` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`));


CREATE TABLE `record_detail` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `record_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `mark` INT NULL,
  `is_absent` TINYINT(1) NULL DEFAULT 0,
  `result` INT NOT NULL,
  `line` INT NOT NULL,
  `sheet` INT NOT NULL,
  PRIMARY KEY (`id`));


ALTER TABLE `record_detail` 
ADD INDEX `index2` (`record_id` ASC),
ADD INDEX `index3` (`student_id` ASC);


ALTER TABLE `record_detail` 
ADD CONSTRAINT `fk_record_detail_1`
  FOREIGN KEY (`record_id`)
  REFERENCES `record` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_record_detail_2`
  FOREIGN KEY (`student_id`)
  REFERENCES `student` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

CREATE TABLE `record_sheet` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `record_id` INT NOT NULL,
  `sheet` INT NOT NULL,
  `physical_sheet` INT NULL,
  `book_id` INT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `record_sheet` 
ADD INDEX `index2` (`record_id` ASC);

ALTER TABLE `record_sheet` 
ADD INDEX `index3` (`book_id` ASC);

ALTER TABLE `record_sheet` 
ADD CONSTRAINT `fk_record_sheet_1`
  FOREIGN KEY (`record_id`)
  REFERENCES `record` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_record_sheet_2`
  FOREIGN KEY (`book_id`)
  REFERENCES `book` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;