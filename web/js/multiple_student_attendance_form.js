function reload(url)
{
  var day = jQuery('#multiple_student_attendance_day').val();
  var year = jQuery('#multiple_student_attendance_year').val();
  var division_id = jQuery('#multiple_student_attendance_division_id').val();
  var career_school_year_id = jQuery('#multiple_student_attendance_career_school_year_id').val();
  
  window.location = url +'?day='+day+'&division_id='+division_id+'&career_school_year_id='+career_school_year_id+'&year='+year;
}

function reload_subject(url)
{
  var day = jQuery('#multiple_student_attendance_subject_day').val();
  var year = jQuery('#multiple_student_attendance_subject_year').val();
  var course_subject_id = jQuery('#multiple_student_attendance_subject_course_subject_id').val();
  var career_school_year_id = jQuery('#multiple_student_attendance_subject_career_school_year_id').val();

  window.location = url +'?day='+day+'&course_subject_id='+course_subject_id+'&career_school_year_id='+career_school_year_id+'&year='+year;
}