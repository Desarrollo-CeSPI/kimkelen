insert into sf_guard_permission (name, description) values ('edit_conduct','Editar notas de conducta');
update sf_guard_permission set  description = 'Imprimir boletines' where name = 'print_report_card';
insert into sf_guard_permission (name, description) values ('print_analytic','Imprimir analíticos');
insert into sf_guard_permission (name, description) values ('print_graduate_certificate','Imprimir constancias de egresados');
insert into sf_guard_permission (name, description) values ('print_regular_certificate','Imprimir constancias de alumno regular');
insert into sf_guard_permission (name, description) values ('print_free_certificate','Imprimir constancias de alumno libre');
insert into sf_guard_permission (name, description) values ('print_withdrawn_certificate','Imprimir constancias de alumno retirado');


