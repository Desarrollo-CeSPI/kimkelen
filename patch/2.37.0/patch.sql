ALTER TABLE `career` 
ADD COLUMN `araucano_code` INT(11) NULL AFTER `resolution_number`;

ALTER TABLE `orientation` 
ADD COLUMN `araucano_code` INT(11) NULL AFTER `name`;

ALTER TABLE `sub_orientation` 
ADD COLUMN `araucano_code` INT(11) NULL AFTER `orientation_id`;