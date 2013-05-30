<?php

/**
 * Validates dates represented by a string.
 *
 * @author Matías A. Torres <torresmat@gmail.com>
 */
class mtValidatorDateString extends sfValidatorDate
{
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
    $this->addOption('date_separator', '/');
  }

  protected function doClean($value)
  {
    if (!is_array($value))
    {
      $dateSeparator = $this->getOption('date_separator');
      $dateArray = explode($dateSeparator, $value);
      if (count($dateArray) != 3)
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
      else
      {
        $value = array();
        $value['day']   = $dateArray[0];
        $value['month'] = $dateArray[1];
        $value['year']  = $dateArray[2];
      }
    }
 
    return parent::doClean($value);
  }
}