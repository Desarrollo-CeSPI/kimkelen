create table student_reserve_status_record (id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, student_id INT(11) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL);
ALTER TABLE student_reserve_status_record ADD INDEX (student_id);
