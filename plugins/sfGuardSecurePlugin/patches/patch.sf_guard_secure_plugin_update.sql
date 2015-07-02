ALTER TABLE `sf_guard_user` ADD `change_password_at` datetime DEFAULT NULL;
ALTER TABLE `sf_guard_user` ADD `must_change_password` TINYINT NOT NULL DEFAULT 0;

CREATE TABLE `sf_guard_secure_login_failure`
(
	`username` VARCHAR(128)  NOT NULL,
	`ip_address` VARCHAR(50)  NOT NULL,
	`cookie_id` VARCHAR(255)  NOT NULL,
	`failed_at` DATETIME  NOT NULL,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	KEY `username_index`(`username`),
	KEY `ip_index`(`ip_address`),
	KEY `failed_at_index`(`failed_at`)
)Type=InnoDB;

CREATE TABLE `sf_guard_secure_password_policy_history`
(
	`username` VARCHAR(128)  NOT NULL,
	`algorithm` VARCHAR(128) default 'sha1' NOT NULL,
	`salt` VARCHAR(128)  NOT NULL,
	`password` VARCHAR(128)  NOT NULL,
	`changed_at` DATETIME  NOT NULL,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	UNIQUE KEY `sf_guard_secure_password_policy_history_U_1` (`username`),
	KEY `username_index`(`username`),
	KEY `changed_at_index`(`changed_at`),
	KEY `algorithm_index`(`algorithm`),
	KEY `salt_index`(`salt`)
)Type=InnoDB;