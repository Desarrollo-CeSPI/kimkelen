<?php
class sfCaptchaGDValidator extends sfValidatorString
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * length: The Length of the string
   *
   * Available error codes:
   *
   *  * length
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  
  public function configure($options = array(), $messages = array())
  {
    $this->addMessage('length', '"%value%" must be %length% characters long.');
    
    $this->addMessage('invalid', 'The numbers you typed in are invalid.');

    $this->addOption('length');

    $this->setOption('empty_value', '');
  }
  
  protected function doClean($value)
  { 
    $clean = (string) $value;
    
    $length = function_exists('mb_strlen') ? mb_strlen($clean, $this->getCharset()) : strlen($clean);

    if ($this->hasOption('length') && $length != $this->getOption('length'))
    {
      throw new sfValidatorError($this, 'length', array('value' => $value, 'length' => $this->getOption('length')));
    }
        
    if (sfContext::getInstance()->getUser()->getAttribute('captcha', NULL) != $clean)
    {
      sfContext::getInstance()->getUser()->setAttribute('captcha', NULL);
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    return $clean;
  }
}
?>
