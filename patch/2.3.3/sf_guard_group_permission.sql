insert into sf_guard_group_permission (group_id, permission_id) values ((select id from sf_guard_group where sf_guard_group.name = 'Preceptor'),(select id from sf_guard_permission where sf_guard_permission.name = 'new_division'));
insert into sf_guard_permission (name,description) values ('close_course_period', 'Poder cerrar el periodo en una comisión');
insert into sf_guard_group_permission (group_id, permission_id) values ((select id from sf_guard_group where sf_guard_group.name = 'Administrador'),(select id from sf_guard_permission where sf_guard_permission.name = 'close_course_period'));
insert into sf_guard_group_permission (group_id, permission_id) values ((select id from sf_guard_group where sf_guard_group.name = 'Jefe de preceptores'),(select id from sf_guard_permission where sf_guard_permission.name = 'close_course_period'));


