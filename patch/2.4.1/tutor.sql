
CREATE TABLE `occupation_category`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(256)  NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `occupation_category_U_1` (`name`)
)Type=InnoDB;

ALTER TABLE `tutor` ADD `nationality` INTEGER default 0 COMMENT 'La nacionalidad del tutor (nativo, naturalizado, extrangero)';
ALTER TABLE `tutor` ADD `occupation_category_id` INTEGER default null;
ALTER TABLE `tutor` ADD `study_id` INTEGER default null;
ALTER TABLE `tutor` ADD  INDEX `tutor_FI_4` (`occupation_category_id`);
ALTER TABLE `tutor` ADD  INDEX `tutor_FI_5` (`study_id`);
ALTER TABLE `tutor` ADD CONSTRAINT `tutor_FK_4`
		FOREIGN KEY (`occupation_category_id`)
		REFERENCES `occupation_category` (`id`)
		ON DELETE RESTRICT;
ALTER TABLE `tutor` ADD CONSTRAINT `tutor_FK_5`
		FOREIGN KEY (`study_id`)
		REFERENCES `study` (`id`)
		ON DELETE RESTRICT;

ALTER TABLE `study` ADD `name` VARCHAR(255)  NOT NULL;
ALTER TABLE `study` DROP `type`;
ALTER TABLE `study` DROP `place`;

INSERT INTO sf_guard_permission (name, description) VALUES ('edit_study', 'Poder editar los estudios cursados');
INSERT INTO sf_guard_permission (name, description) VALUES ('show_study', 'Poder ver/listar los estudios cursados');
