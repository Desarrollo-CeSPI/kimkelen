create table delete_sacs 
  (
    select sacs.id 
      from 
        student_disapproved_course_subject sdcs inner join course_subject_student css 
        on sdcs.course_subject_student_id = css.id inner join student_approved_course_subject sacs 
        on sacs.course_subject_id = css.course_subject_id and sacs.student_id = css.student_id
  );
delete from student_approved_course_subject where id in (select id  from delete_sacs);
