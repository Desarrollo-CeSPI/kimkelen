ALTER TABLE `student_attendance`  MODIFY `value` decimal(3, 2);
ALTER TABLE `absence_type`  MODIFY `value` decimal(3, 2);
UPDATE  `absence_type` SET  `value` =  '0.16' WHERE  `absence_type`.`value` ='0.17';
update student_attendance as sa set value = (select at.value from absence_type as at where at.id = sa.absence_type_id);