INSERT INTO `sf_guard_permission` (`id` ,`name` ,`description`)
VALUES (NULL , 'edit_student_attendance_history', 'Editar asistencias de cualquier día además del día de la fecha');

INSERT INTO sf_guard_group_permission (group_id ,permission_id)
VALUES ((SELECT id FROM sf_guard_group WHERE name='Preceptor'), (SELECT id FROM sf_guard_permission WHERE name= 'edit_student_attendance_history'));
INSERT INTO sf_guard_group_permission (group_id ,permission_id)
VALUES ((SELECT id FROM sf_guard_group WHERE name='Administrador'), (SELECT id FROM sf_guard_permission WHERE name= 'edit_student_attendance_history'));
INSERT INTO sf_guard_group_permission (group_id ,permission_id)
VALUES ((SELECT id FROM sf_guard_group WHERE name LIKE '%Simil administra%'), (SELECT id FROM sf_guard_permission WHERE name= 'edit_student_attendance_history'));
INSERT INTO sf_guard_group_permission (group_id ,permission_id)
VALUES ((SELECT id FROM sf_guard_group WHERE name='Jefe de preceptores'), (SELECT id FROM sf_guard_permission WHERE name= 'edit_student_attendance_history'));
