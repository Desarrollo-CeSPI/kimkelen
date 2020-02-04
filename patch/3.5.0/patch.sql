CREATE TABLE `guard_user_social` (`id` INTEGER  NOT NULL AUTO_INCREMENT, `social_id` VARCHAR(255)  NOT NULL, `user_id` INTEGER, PRIMARY KEY (`id`), INDEX `guard_user_social_FI_1` (`user_id`), CONSTRAINT `guard_user_social_FK_1` FOREIGN KEY (`user_id`) REFERENCES `sf_guard_user` (`id`) ON DELETE CASCADE )Engine=InnoDB;

CREATE TABLE `token_user`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`sf_guard_user_id` INTEGER  NOT NULL,
	`token` VARCHAR(50),
	`created_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `token_user_FI_1` (`sf_guard_user_id`),
	CONSTRAINT `token_user_FK_1`
		FOREIGN KEY (`sf_guard_user_id`)
		REFERENCES `sf_guard_user` (`id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
)Engine=InnoDB COMMENT='Tokens válidos para reseteo de contraseña';
