<?php

/**
 * This validator is used for alWidgetFormTimepicker
 * validations. It allows to validate for single values
 * and also time range values.
 *
 * If using range values, the enable_timerange should be
 * set to True.
 *
 * @author Alvaro F. Lara <alvarofernandolara@gmail.com>
 */
class alValidatorTimepicker extends sfValidatorBase
{

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addOption('enable_timerange', false);
  }

  /**
   * Returns true if the value is empty.
   *
   * @param  mixed $value  The input value
   *
   * @return bool true if the value is empty, false otherwise
   */
  protected function isEmpty($value)
  {
    return in_array($value, array(null, '-', array()), true);
  }


  protected function doClean($value)
  {
    if($this->getOption('enable_timerange')){
      $value = explode('-',$value);

      if(count($value) < 2)
      {
        throw new sfValidatorError($this, 'You are using a range validator with a single value widget. Please verify.');
      }

      if ($this->checkSingleHourFormat($value[0]) && $this->checkSingleHourFormat($value[1]) && $this->smallerThan($value[0],$value[1]))
      {
        return implode('-',$value);
      }
      else
      {
        throw new sfValidatorError($this, 'invalid');
      }
    } else {

      if(!is_string($value))
      {
        throw new sfValidatorError($this, 'You are using a single validator with a range value widget. Please verify.');
      }

      if (!$this->checkSingleHourFormat($value))
      {
        throw new sfValidatorError($this, 'invalid');
      }
    }

    return $value;
  }

  protected function checkSingleHourFormat($value)
  {
    $values = explode(':',$value);
    $check = count($values) > 1;
    $check = $check && (is_numeric($values[0]) && ((intval($values[0])) >= 0) && ((intval($values[0])) < 24)) && 
            (is_numeric($values[1]) && ((intval($values[1])) >= 0) && ((intval($values[1])) < 60));
    return $check;
  }

  protected function smallerThan($v1,$v2)
  {
    $v1_values = explode(':',$v1);
    $v2_values = explode(':',$v2);

    return ((intval($v1_values[0]) <= intval($v2_values[0])) || (((intval($v1_values[0]) == intval($v2_values[0])) && (intval($v1_values[1]) < intval($v2_values[1])))));
  }
}
