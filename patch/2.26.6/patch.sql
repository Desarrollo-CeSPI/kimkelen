ALTER TABLE `conduct` ADD `shift_id` INT NULL ;
ALTER TABLE `conduct` ADD INDEX ( `shift_id` ) ;
ALTER TABLE `conduct` ADD FOREIGN KEY ( `shift_id` ) REFERENCES `shift` ( `id` ) ON DELETE RESTRICT ON UPDATE RESTRICT ;
