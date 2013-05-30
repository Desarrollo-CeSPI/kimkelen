function disableColumn(day)
{
	var name = "multiple_student_attendance_day_disabled_" + day;
	disabled = (jQuery("#"+name).is(":checked")) ? true : false;	
	jQuery(".day_" + day).children().attr('disabled',disabled); 
}

function disableDay(day)
{
	var name = "multiple_student_attendance_day_disabled_" + day;
	jQuery("#"+name).attr("checked", true);
}