/*actualizo los indices de la tabla address */
update address a , migra_city m  set a.city_id = m.codigo_nuevo, a.department_id = m.department_id, a.state_id = m.state_id where a.city_id = m.city_id;
update address set state_id = 5406 where state_id = 2;
update address set state_id = 5430 where state_id = 3;

/*actualizo los indices de la tabla person*/
update person p, migra_city m  set p.birth_city = m.codigo_nuevo , p.birth_department = m.department_id , p.birth_state = m.state_id , p.birth_country = m.country_id where birth_city = m.city_id;
update person set birth_state = 5406 where birth_state = 2;
update person set birth_state = 5454 where birth_state = 10;
update person set birth_state = 5462 where birth_state = 20;
update person set birth_country = 54 where birth_country = 13;
update person set birth_country = 51 where birth_country = 173;

/*agrego indices eliminados.*/
ALTER TABLE `address` ADD INDEX ( `city_id` ) ;
ALTER TABLE `address` ADD FOREIGN KEY ( `city_id` ) REFERENCES `city` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;

ALTER TABLE `city` ADD INDEX ( `department_id` ) ;
ALTER TABLE `city` ADD FOREIGN KEY ( `department_id` ) REFERENCES `department` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;

ALTER TABLE `department` ADD INDEX ( `state_id` ) ;
ALTER TABLE `department` ADD FOREIGN KEY ( `state_id` ) REFERENCES `state` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;

ALTER TABLE `state` ADD INDEX ( `country_id` ) ;
ALTER TABLE `state` ADD FOREIGN KEY ( `country_id` ) REFERENCES `country` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;


ALTER TABLE `address` ADD INDEX ( `department_id` ) ;
ALTER TABLE `address` ADD FOREIGN KEY ( `department_id` ) REFERENCES `department` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;

ALTER TABLE `address` ADD INDEX ( `state_id` ) ;
ALTER TABLE `address` ADD FOREIGN KEY ( `state_id` ) REFERENCES `state` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;

ALTER TABLE `person` ADD INDEX ( `birth_country` ) ;
ALTER TABLE `person` ADD INDEX ( `birth_state` ) ;
ALTER TABLE `person` ADD INDEX ( `birth_department` ) ;
ALTER TABLE `person` ADD INDEX ( `birth_city` ) ;

ALTER TABLE `person` ADD FOREIGN KEY ( `birth_country` ) REFERENCES `country` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;
ALTER TABLE `person` ADD FOREIGN KEY ( `birth_state` ) REFERENCES `state` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;
ALTER TABLE `person` ADD FOREIGN KEY ( `birth_department` ) REFERENCES `department` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;
ALTER TABLE `person` ADD FOREIGN KEY ( `birth_city` ) REFERENCES `city` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;

/*Vaciar tabla origin_school */
TRUNCATE TABLE origin_school;



