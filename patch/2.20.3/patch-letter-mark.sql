CREATE TABLE `letter_mark`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`letter` VARCHAR(255) NOT NULL,
	`value` INTEGER  NOT NULL COMMENT 'valor numerico de la letra',
	PRIMARY KEY (`id`)
)Engine=InnoDB COMMENT='Representa la conversion de letras a notas';

INSERT INTO  `letter_mark` 
(
	`letter` ,
	`value`
) 
VALUES ('A', 4), ('S', 7), ('B', 8), ('D', 9), ('E', 10)