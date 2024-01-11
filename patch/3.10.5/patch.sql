ALTER TABLE `analytic` 
ADD COLUMN `previous_certificate` VARCHAR(255) NULL DEFAULT NULL AFTER `observations`;
