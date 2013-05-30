SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `absence_type` ADD `method` INTEGER default 0 NOT NULL;

insert into absence_type(name, value, method) VALUES('presente',0,1),('ausente',1,1),('tardanza',0.5,1);


