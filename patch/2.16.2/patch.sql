ALTER TABLE `student_examination_repproved_subject` ADD `date` DATE COMMENT 'Fecha en que el alumno rinde';
ALTER TABLE `career_subject_school_year` DROP INDEX career_subject_school_year, ADD UNIQUE INDEX `career_subject_school_year` (`career_subject_id`, `career_school_year_id`);
