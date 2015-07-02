<?php

class mtUtility
{
  static public function convertToChoices($objects, $custom = array(), $value_method = '__toString', $key_method = 'getPrimaryKey', $translate = true)
  {
    if ($translate)
      sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    $choices = array();
    foreach ($custom as $key => $value)
    {
      $choices[$key] = $value;
    }
    foreach ($objects as $o)
    {
      $choices[$o->$key_method()] = $translate? __($o->$value_method()) : $o->$value_method();
    }
    return $choices;
  }
}
