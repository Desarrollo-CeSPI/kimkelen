CREATE TABLE `change_status_motive`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255)  NOT NULL,
	`status_id` INTEGER  NOT NULL COMMENT 'Referencia el estado al cual esta asociado el motivo',
	PRIMARY KEY (`id`)
)Engine=InnoDB COMMENT='Cada tupla representa un motivo por el cual el alumno cambio de estado';

ALTER TABLE `student_career_school_year` ADD `change_status_motive_id` INTEGER default null COMMENT 'Especifica el motivo del cambio de estado', DROP INDEX student_career_school_year, ADD  INDEX `student_career_school_year` (`student_id`, `career_school_year_id`, `change_status_motive_id`);

insert into sf_guard_permission (name, description) values ('edit_change_status_motive','Crear, editar y eliminar motivos de cambios de estado');
insert into sf_guard_permission (name, description) values ('show_change_status_motive','Listar y ver detalles de motivos de cambios de estado');