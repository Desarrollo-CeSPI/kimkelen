
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- nc_change_log_entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `nc_change_log_entry`;


CREATE TABLE `nc_change_log_entry`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`class_name` VARCHAR(255)  NOT NULL,
	`object_pk` INTEGER  NOT NULL,
	`changes_detail` TEXT  NOT NULL,
	`username` VARCHAR(255),
	`operation_type` INTEGER  NOT NULL,
	`created_at` DATETIME,
	PRIMARY KEY (`id`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
