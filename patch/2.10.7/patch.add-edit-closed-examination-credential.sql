insert into 
  sf_guard_permission (name, description) 
  values ('edit_closed_examination', 'Editar calificaciones de mesas de examen cerradas');

insert into 
  sf_guard_group_permission (permission_id, group_id)
  values (
    (select sf_guard_permission.id from sf_guard_permission where sf_guard_permission.name = 'edit_closed_examination'),
    (select sf_guard_group.id from sf_guard_group where sf_guard_group.name = 'Administrador')
  );

