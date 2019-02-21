ALTER TABLE `school_year_student` ADD `is_deleted` BOOLEAN NOT NULL DEFAULT FALSE ;
ALTER TABLE `student` ADD COLUMN `judicial_restriction` TINYINT(4) NULL DEFAULT 0 AFTER `origin_school_id`;
