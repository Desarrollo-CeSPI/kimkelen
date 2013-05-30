<?php

/**
 */
class mtValidatorCuilExtended extends mtValidatorCuil
{
  public function doClean($value)
  {
    if (is_array($value)
        && isset($value['prefix'])
        && isset($value['middle'])
        && isset($value['suffix']))
    {
      $value = trim($value['prefix']).trim($value['middle']).trim($value['suffix']);
    }

    return parent::doClean($value);
  }

  protected function isEmpty($value)
  {
    if (is_array($value)
        && isset($value['prefix'])
        && isset($value['middle'])
        && isset($value['suffix']))
    {
      $value = trim($value['prefix']).trim($value['middle']).trim($value['suffix']);
    }

    return empty($value);
  }
}
