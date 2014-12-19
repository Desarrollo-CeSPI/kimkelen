ALTER TABLE  `address` DROP FOREIGN KEY  `address_FK_1` ;
ALTER TABLE  `state` DROP FOREIGN KEY  `state_FK_1`;

-- Guardo viejos ID

update person set old_birth_city = birth_city;
update address set old_city_id = city_id;

-- ACA HAY QUE MAPEAR ID

update  address a inner join
  map_city m on a.city_id = m.k_lvm_id
set a.city_id = m.s_id;

update  person p inner join
  map_city  m on p.birth_city = m.k_lvm_id
set p.birth_city  = m.s_id;


-- Vaciar tablas
truncate table city;
truncate table state;
truncate table country;

