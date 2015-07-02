<?php

/**
 */
class mtValidatorCuil extends sfValidatorString
{

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
  }

  protected function doClean($value)
  {
    $value      = parent::doClean($value);
    $value      = str_replace(
                    array('-', '/', '\\', '_'),
                    '',
                    $value
                  );

    if (!CuitVerifier::verify($value))
    {
      throw new sfValidatorError($this, 'invalid');
    }

    return $value;
  }
}
