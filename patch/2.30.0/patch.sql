ALTER TABLE `analytic` 
ADD COLUMN `certificate_number` VARCHAR(20) NULL DEFAULT NULL AFTER `created_at`;