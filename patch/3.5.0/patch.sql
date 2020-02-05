CREATE TABLE `guard_user_social` (`id` INTEGER  NOT NULL AUTO_INCREMENT, `social_id` VARCHAR(255)  NOT NULL, `user_id` INTEGER, PRIMARY KEY (`id`), INDEX `guard_user_social_FI_1` (`user_id`), CONSTRAINT `guard_user_social_FK_1` FOREIGN KEY (`user_id`) REFERENCES `sf_guard_user` (`id`) ON DELETE CASCADE )Engine=InnoDB;

CREATE TABLE `token_user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sf_guard_user_id` INT NOT NULL,
  `token` VARCHAR(50) NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `token_user` 
ADD INDEX `index2` (`sf_guard_user_id` ASC);

ALTER TABLE `token_user` 
ADD CONSTRAINT `fk_token_1`
  FOREIGN KEY (`sf_guard_user_id`)
  REFERENCES `sf_guard_user` (`id`)
  ON UPDATE CASCADE
  ON DELETE RESTRICT;