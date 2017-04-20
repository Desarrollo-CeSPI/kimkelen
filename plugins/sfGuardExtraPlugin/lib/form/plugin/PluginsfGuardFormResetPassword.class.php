<?php

/**
 * PluginsfGuardFormResetPassword
 * @package    symfony
 * @subpackage form
 */
class PluginsfGuardFormResetPassword extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'password'         => new sfWidgetFormInputPassword(),
      'password_confirm' => new sfWidgetFormInputPassword(),
    ));

    $password_policy_validator_class = sfConfig::get('app_sf_guard_secure_password_validator', 'sfGuardSecurePasswordValidator');

    $this->setValidators(array(
      'password'         => new sfValidatorAnd(array(new sfValidatorString(array('max_length' => 128)), new $password_policy_validator_class())),
      'password_confirm' => new sfValidatorString(array('max_length' => 128)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_confirm', array(), array('invalid' => 'The two passwords do not match'))
    );

    $this->widgetSchema->setNameFormat('reset[%s]');
  }

  /**
   * Bind values and save new password
   * @param  array   $values tainted values
   * @return boolean
   */
  public function bindAndSave(array $values)
  {
    $this->bind($values);
    if ($this->isValid())
    {
      $user = sfGuardUserPeer::retrieveByPK($this->getOption('userid'));
      $user->setPassword($values['password']);
      $user->getMustChangePassword(false);
      $user->save();
      TokenUserPeer::deleteUsedTokenFor($user);
      return true;
    }
    else
    {
      return false;
    }
  }
}
