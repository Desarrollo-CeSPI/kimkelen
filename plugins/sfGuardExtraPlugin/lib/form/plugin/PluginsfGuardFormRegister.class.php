<?php

/**
 * PluginsfGuardFormRegister
 * @package    symfony
 * @subpackage form
 */
class PluginsfGuardFormRegister extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'username'         => new sfWidgetFormInputText(),
      'email'            => new sfWidgetFormInputText(),
      'password'         => new sfWidgetFormInputPassword(),
      'password_confirm' => new sfWidgetFormInputPassword(),
    ));

    $this->setValidators(array(
      'username'         => new sfValidatorString(array('trim' => true), array('required' => 'Your username is required.')),
      'email'            => new sfValidatorEmail(array('trim' => true), array('required' => 'Your e-mail address is required.', 'invalid' => 'The email address is invalid.')),
      'password'         => new sfValidatorString(array('min_length' => 8), array('min_length' => 'Password is too short (%min_length% characters min).', 'required' => 'Your password is required.')),
      'password_confirm' => new sfValidatorString(array(), array('required' => 'Your password confirmation is required.')),
    ));

    $this->validatorSchema->setPostValidator(new sfValidatorAnd(array(
      new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_confirm', array(), array('invalid' => 'The two passwords do not match')),
      new sfValidatorPropelUnique(array('trim' => true, 'model' => 'sfGuardUser', 'column' => array('username')), array('invalid' => 'This username already exists. Please choose another one.')),
    )));

    $this->widgetSchema->setNameFormat('register[%s]');
  }
}
