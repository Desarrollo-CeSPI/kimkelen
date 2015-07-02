<?php

/**
 * This validator adds the posibility to set a list of 'invalid days'. 
 * This list if made of day names with the php name of the day as value ('Mon', 'Tue', ..., etc) and a string to show as key.
 * Anyway, there are some constants defined that should be used instead of the php day names to avoid breaking the encapsulation of the widget.
 * 
 */
class mtValidatorDateExtended extends mtValidatorDateString
{
  const
    SUNDAY    = 'Sun',
    MONDAY    = 'Mon',
    TUESDAY   = 'Tue',
    WEDNESDAY = 'Wed',
    THURSDAY  = 'Thu',
    FRIDAY    = 'Fri',
    SATURDAY  = 'Sat',

    SUNDAY_STRING     = 'Sunday',
    MONDAY_STRING     = 'Monday',
    TUESDAY_STRING    = 'Tuesday',
    WEDNESDAY_STRING  = 'Wednesday',
    THURSDAY_STRING   = 'Thursday',
    FRIDAY_STRING     = 'Friday',
    SATURDAY_STRING   = 'Saturday';

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
    $this->addMessage('invalid_day', "Invalid day selected. Day cannot be %values%");
    $this->addOption('invalid_days', array());
    $this->addOption('days_string_terminator', 'etc.');
  }

  protected function getDaysString()
  {
    $string = '';
    foreach ($this->getOption('invalid_days') as $day_string => $day_code)
    {
      if (!empty($string)) $string .= ', ';
      $string .= "'$day_string'";
    }
    if (!empty($string)) $string .= ', '.$this->getOption('days_string_terminator');
    return $string;
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $value      = parent::doClean($value);
    $timestamp  = strtotime($value);
    $day        = date("D", $timestamp);

    foreach ($this->getOption('invalid_days') as $day_string => $day_code)
    {
      if (strcmp($day, $day_code) == 0)
      {
        throw new sfValidatorError($this, 'invalid_day', array('values' => $this->getDaysString()));
      }
    }

    return $value;
  }
}
