ALTER TABLE `student_attendance` ADD UNIQUE INDEX `student_attendance_unique` (`student_id`, `day`,`course_subject_id`);
