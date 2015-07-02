ALTER TABLE `student_advice` ADD `request_date` DATE  NOT NULL, ADD `resolution_date` DATE;
UPDATE `student_advice` SET `request_date`= `day`;
ALTER TABLE `student_advice` DROP `day`;