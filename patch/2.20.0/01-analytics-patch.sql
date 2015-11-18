CREATE TABLE `analytic`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`description` VARCHAR(255) COMMENT 'Alguna descripcion necesaria en el certificado',
	`career_student_id` INTEGER  NOT NULL COMMENT 'Referencia al estudiante en la carrera',
	`certificate` LONGBLOB COMMENT 'Archivo del certificado',
	`created_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `analytic_FI_1` (`career_student_id`),
	CONSTRAINT `analytic_FK_1`
		FOREIGN KEY (`career_student_id`)
		REFERENCES `career_student` (`id`)
)Engine=InnoDB COMMENT='Representa un certificado analitico emitido';


