-- fix BBA absences
update student_attendance set absence_type_id=NULL where course_subject_id IS NOT NULL;

-- new permission
insert into sf_guard_permission(name, description) values('show_student_min', 'Ver detalle reducido de alumnos');
