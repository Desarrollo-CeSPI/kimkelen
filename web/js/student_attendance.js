function disableColumn(day)
{
	var name = "multiple_student_attendance_day_disabled_" + day;
	disabled = (jQuery("#"+name).is(":checked")) ? true : false;
	jQuery(".day_" + day).children().attr('disabled',disabled);
	jQuery(".day_" + day).css('background', (disabled) ? 'lightgray' : 'none');
}

function disableDay(day)
{
	var name = "multiple_student_attendance_day_disabled_" + day;
	jQuery("#"+name).attr("checked", true);
}

function disableDayUneditable(day)
{
	var name = "multiple_student_attendance_day_disabled_" + day;
	jQuery("#"+name).attr("checked", true);
	jQuery("#"+name).attr("disabled", true);
}