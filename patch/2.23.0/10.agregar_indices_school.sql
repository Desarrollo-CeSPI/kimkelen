update `student` set origin_school_id = null where origin_school_id = 69003501; /* el id de origin_school pertneec a escuela "NO CORRESPONDE"*/
ALTER TABLE `origin_school` ADD INDEX ( `city_id` ) ;
ALTER TABLE `origin_school` ADD FOREIGN KEY ( `city_id` ) REFERENCES `city` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;
ALTER TABLE `student` ADD FOREIGN KEY ( `origin_school_id` ) REFERENCES `origin_school` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;
DROP table migra_city;