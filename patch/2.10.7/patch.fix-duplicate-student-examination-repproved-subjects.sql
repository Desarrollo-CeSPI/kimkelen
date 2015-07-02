create temporary table to_delete 
  (select sers.id 
    from student_examination_repproved_subject as sers 
    where mark is null and is_absent = 0 
    and exists 
    (select * 
      from student_examination_repproved_subject as sers2 
      where sers.student_repproved_course_subject_id = sers2.student_repproved_course_subject_id and sers.examination_repproved_subject_id = sers2.examination_repproved_subject_id and sers.id <> sers2.id)
    and sers.id not in 
    (select sers2.id 
      from student_examination_repproved_subject as sers2 
      where sers.student_repproved_course_subject_id = sers2.student_repproved_course_subject_id and sers.examination_repproved_subject_id = sers2.examination_repproved_subject_id and sers.id <> sers2.id));
delete from student_examination_repproved_subject where student_examination_repproved_subject.id in (select id from to_delete);
