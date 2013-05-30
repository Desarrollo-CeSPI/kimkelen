function closeStudent(id, id_check)
{  
  if (jQuery('#'+id_check).is(':checked'))
  {
    jQuery('#'+id +'_close_div').show();
  }
  else
  {
    jQuery('#'+id +'_close_div').hide();
  }
  
}

function change (id, id_check)
{
  jQuery("#close_student_" + id).hide();
  jQuery("#course_student_mark_"+ id_check + "_" + id + "_close").attr('checked', '');
}