/**
 *  Functions for course_student_marks
 *
 *  @author tcordoba
 */

//Show/hide the multiple-marks-div
function showMultipleCalification()
{
  jQuery('#multiple').toggle();
}

//This function select the correct setMark function
function setSameMark()
{
  switch (jQuery('#students_selection').val())
  {
    case '0':
      setMarkToSelected(true);
      break;
    case '1':
      setMarkToSelected(false);
      break;
    case '2':
      setMarkToEmptys();
      break;
  }
}

// Set mark to an individual input
function setMarkToInput(element, mark)
{
  var input = jQuery(element);
  input.val(mark);
  input.animate({backgroundColor: '#FF5'}, 1000)
  input.animate({backgroundColor: '#FFF'}, 1000);
}

//This function it's called when the user wants to set the same mark to all the students with empty value in that mark
function setMarkToEmptys()
{
  var mark_value  = jQuery('#multiple_mark').val();
  var mark_number = jQuery('#mark_choice').val();

  if (mark_number != 0 && mark_number != null)
  {
    jQuery.each(jQuery('.mark_'+mark_number), function () {
      if (!jQuery(this).val())
      {
        setMarkToInput(this, mark_value);
      }
    });
  }
}

//This function it's called when the user wants to set the same mark to all selected/unselected students
function setMarkToSelected(selected_state)
{
  var mark_value  = jQuery('#multiple_mark').val();
  var mark_number = jQuery('#mark_choice').val();

  if (mark_number != 0 && mark_number != null)
  {
    jQuery.each(jQuery('.mark_'+mark_number), function () {
      if (jQuery(this).closest('tr').find('.checkbox').attr('checked') == selected_state)
      {
        setMarkToInput(this, mark_value);
        jQuery(this).change();
      }
    });
  }
}



//This function it's called when the user wants to set the same configuration to all selected/unselected students
function setStateOnAverage()
{
  jQuery.each(jQuery('.course_status'), function () {
      setConfigurationStateToElement(this);
  });
}

//This function set the course student status, that depends on the configuration promotion note of the course/subject
function setConfigurationStateToElement(element)
{
  var select = jQuery(element);
  var subject_configuration_mark = jQuery(element).closest('tr').find('.regular_mark').val();
  if ((subject_configuration_mark) > 0)
  {
    if (subject_configuration_mark <= getAverage(element))
      {
        select
          .val(1)
      }
    else
      {
        select
          .val(2)
      }
  }
  else
  {
    select
      .val(0)
  }
  select
    .closest('li')
    .animate({ backgroundColor: '#ff5'}, 1000)
    .animate({ backgroundColor: '#fff'}, 1000);
}
