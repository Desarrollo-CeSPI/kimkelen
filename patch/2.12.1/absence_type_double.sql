ALTER TABLE `student_attendance`  MODIFY `value` double;
ALTER TABLE `absence_type`  MODIFY `value` double;
UPDATE  `absence_type` SET  `value` =  '0.33333333333' WHERE  `absence_type`.`value` ='0.33';
UPDATE  `absence_type` SET  `value` =  '0.16666666667' WHERE  `absence_type`.`value` ='0.17';
update student_attendance as sa set value = (select at.value from absence_type as at where at.id = sa.absence_type_id);
