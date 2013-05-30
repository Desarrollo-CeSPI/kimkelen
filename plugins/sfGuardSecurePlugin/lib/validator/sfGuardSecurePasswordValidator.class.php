<?php

/**
 * Password validator for enforced password policy.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class sfGuardSecurePasswordValidator extends sfValidatorRegex
{
  public function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setOption('pattern', $this->getPasswordPattern());

    $this->setMessage('invalid', 'The password is too weak. It must be at least 8 characters long AND contain at least 2 alphabetical characters AND 2 digital characters AND one symbol among !@#$%^&*-.');
  }

  protected function getPasswordPattern()
  {
    return '/^(?=(?:.*[a-z]){2})(?=(?:.*\d){2})(?=(?:.*[!@#$%^&*-]){1}).{8,}$/';
  }
}
