<?php
class dcValidatorDateTimePicker extends sfValidatorDateTime
{

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $date_validator = new mtValidatorDateString();
    $time_validator = new alValidatorTimepicker();
    
    return $date_validator->clean($value['date']).' '.$time_validator->clean($value['time']);
  }
}
