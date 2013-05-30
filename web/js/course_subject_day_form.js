function course_subject_day_form_on_click_handler(js_id)
{
  disabled = (jQuery("#"+js_id+"_enable").is(":checked")) ? false : true;
  jQuery("#"+js_id+"_classroom_id").attr("disabled", disabled);
  jQuery("#"+js_id+"_ends_at_hour").attr("disabled", disabled);
  jQuery("#"+js_id+"_ends_at_minute").attr("disabled", disabled);
  jQuery("#"+js_id+"_starts_at_hour").attr("disabled", disabled);
  jQuery("#"+js_id+"_starts_at_minute").attr("disabled", disabled);

}