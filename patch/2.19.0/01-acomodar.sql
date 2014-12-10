ALTER TABLE  `address` DROP FOREIGN KEY  `address_FK_1` ;

-- ACA HAY QUE MAPEAR ID


-- Vaciar tablas
truncate table city;
truncate table state;
truncate table country;


ALTER TABLE  `state` DROP FOREIGN KEY  `state_FK_1` ;