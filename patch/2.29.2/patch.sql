update occupation set name = 'Salud' where id = 18;
insert into occupation(name) values('Profesional independiente');
insert into occupation(name) values('Servicios públicos');
insert into occupation(name) values('Servicios domiciliarios');
insert into occupation(name) values('Construccion');
insert into occupation(name) values('Industria');
update occupation set name = 'Producción primaria (agrícola-ganadera/fruti-hortícola)' where id = 1;
insert into occupation (name) values('Otros');


ALTER TABLE occupation_category
DROP INDEX name ;

insert into occupation_category(name) values('Empleado del sector público');
insert into occupation_category(name) values('Empleado del sector privado');
insert into occupation_category(name) values('Profesional independiente');
update occupation_category set name='Trabajador por cuenta propia' where id = 7;
update occupation_category set name = 'Patrón/empleador de hasta 5 empleados' where id = 8;
update occupation_category set name = 'Patrón/empleador con 5 empleados o más' where id = 9;
update occupation_category set name = 'Servicio doméstico/actividades de cuidado' where id = 15;
insert into occupation_category (name) values('Cooperativista/Beneficiario de un Programa de Empleo');
insert into occupation_category (name) values('Otros');

ALTER TABLE occupation_category
ADD UNIQUE INDEX name_UNIQUE (name ASC);
