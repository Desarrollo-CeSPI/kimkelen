alter table student_career_school_year add change_status_motive_id INT(11) DEFAULT NULL;
ALTER TABLE student_career_school_year ADD change_status_motive_id INTEGER default null, ADD  INDEX student_career_school_year_FI_4 (change_status_motive_id), ADD CONSTRAINT student_career_school_year_FK_4 FOREIGN KEY (change_status_motive_id) REFERENCES change_status_motive (id) ON DELETE RESTRICT, DROP change_status_motive_id;
create table change_status_motive (id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, name VARCHAR(255), status_id INT(11));
insert into sf_guard_permission (name, description) values ('edit_change_status_motive','Crear, editar y eliminar motivos de cambios de estado');
insert into sf_guard_permission (name, description) values ('show_change_status_motive','Listar y ver detalles de motivos de cambios de estado');
