<?php

/**
 * PluginsfGuardFormForgotPassword
 * @package    symfony
 * @subpackage form
 */
class PluginsfGuardFormForgotPassword extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'username_or_email' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'username_or_email' => new sfGuardValidatorUsernameOrEmail(array('trim' => true), array('required' => 'Your username or e-mail address is required.', 'invalid' => 'Username or e-mail address not found please try again.')),
    ));

    $this->widgetSchema->setNameFormat('forgot_password[%s]');
  }
}
