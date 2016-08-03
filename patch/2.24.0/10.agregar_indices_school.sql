ALTER TABLE `origin_school` ADD INDEX ( `city_id` ) ;
ALTER TABLE `origin_school` ADD FOREIGN KEY ( `city_id` ) REFERENCES `city` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;
ALTER TABLE `student` ADD FOREIGN KEY ( `origin_school_id` ) REFERENCES `origin_school` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT ;

