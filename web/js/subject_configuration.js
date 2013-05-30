function updateMaxAbsenceWidget(widget_id, observe_id)
{
  var observe_element = jQuery('#subject_configuration_' + observe_id + '_1');
  var element = jQuery('#subject_configuration_' + widget_id);

  if (observe_element.attr('checked') !== undefined)
  {
    element.attr('disabled', 'disabled');    
  }
  else
  {
    element.attr('disabled', false);  
  }

  
};