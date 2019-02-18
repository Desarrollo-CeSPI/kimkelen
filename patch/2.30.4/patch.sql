ALTER TABLE `analytic` 
ADD COLUMN `observations` VARCHAR(100) NULL DEFAULT NULL AFTER `certificate_number`;
ALTER TABLE `examination_repproved_subject` 
ADD COLUMN `date` DATE NULL DEFAULT NULL after `career_subject_id`;
ALTER TABLE `examination_subject` 
ADD COLUMN `date` DATE NULL DEFAULT NULL AFTER `career_subject_school_year_id`;

