ALTER TABLE `course_subject_student_examination` ADD UNIQUE INDEX `csse_unique` (`course_subject_student_id`, `examination_subject_id`, `examination_number`);

SET foreign_key_checks = 0;
ALTER TABLE  `course_subject_student_mark` ADD FOREIGN KEY (  `course_subject_student_id` ) REFERENCES  `course_subject_student` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `examination_subject` ADD UNIQUE INDEX `examination_subject_unique` (`examination_id`, `career_subject_school_year_id`), DROP INDEX examination_subject_FI_1;

ALTER TABLE  `student_advice` DROP FOREIGN KEY  `student_advice_FK_5` ,
ADD FOREIGN KEY (  `responsible_id` ) REFERENCES  `person` (
`id`
) ON DELETE SET NULL ON UPDATE RESTRICT ;

ALTER TABLE  `sf_guard_user_profile` DROP FOREIGN KEY  `sf_guard_user_profile_FK_1` ,
ADD FOREIGN KEY (  `user_id` ) REFERENCES  `alumnos`.`sf_guard_user` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;