<?php

/**
 * PluginsfGuardFormResetPassword
 * @package    symfony
 * @subpackage form
 */
class PluginsfGuardFormResetPassword extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'password'         => new sfWidgetFormInputPassword(),
      'password_confirm' => new sfWidgetFormInputPassword(),
    ));

    $this->setValidators(array(
      'password'         => new sfValidatorString(array('min_length' => 8), array('min_length' => 'Password is too short (%min_length% characters min).', 'required' => 'Your password is required.')),
      'password_confirm' => new sfValidatorString(array(), array('required' => 'Your password confirmation is required.')),
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
      $user->save();

      return true;
    }
    else
    {
      return false;
    }
  }
}
