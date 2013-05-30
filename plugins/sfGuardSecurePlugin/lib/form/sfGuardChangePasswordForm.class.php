<?php

/**
 * sfGuardUser form.
 *
 * @package    form
 * @subpackage sf_guard_user
 * @version    SVN: $Id: sfGuardUserForm.class.php 13001 2008-11-14 10:45:32Z noel $
 */
class sfGuardChangePasswordForm extends BasesfGuardUserForm
{

  public function configure()
  {
    parent::configure();

    unset(
      $this['username'],
      $this['last_login'],
      $this['created_at'],
      $this['salt'],
      $this['algorithm'],
      $this['is_active'],
      $this['is_super_admin'],
      $this['change_password_at'],
      $this['must_change_password']
    );
    $this->setWidgets(
      array(
        'username'            => new sfWidgetFormInputHidden(),
        'password'            => new sfWidgetFormInputPassword(),
        'password_new'        => new sfWidgetFormInputPassword(),
        'password_new_bis'  =>  new sfWidgetFormInputPassword(),
    ));

    $password_policy_validator_class = sfConfig::get('app_sf_guard_secure_password_validator', 'sfGuardSecurePasswordValidator');

    $this->setValidators(
      array(
        'username'            => new sfValidatorChoice(array('choices'=>array(sfContext::getInstance()->getUser()->getUsername()))),
        'password'            => new sfValidatorString(array('max_length' => 128)),
        'password_new'        => new sfValidatorAnd(array(
                                    new sfValidatorString(array('max_length' => 128)),
                                    new $password_policy_validator_class())),
        'password_new_bis'    => new sfValidatorString(array('max_length' => 128)),
    ));
    $this->getWidgetSchema()->setLabels(
      array(
        'password'            => 'Current',
        'password_new'        => 'New',
        'password_new_bis'    => 'Repeat',
    ));

    $this->setDefaults(array(
          'password'=>null,
          'username'=>sfContext::getInstance()->getUser()->getUsername()));

    $this->getWidgetSchema()->setNameFormat('change_password[%s]');
     
    $this->getValidatorSchema()->setPostValidator(
      new sfValidatorAnd(array(
        new sfGuardValidatorUser(array('check_login_failure'=>false),array('invalid'=>'Current password is invalid')),
        new sfValidatorSchemaCompare('password_new', sfValidatorSchemaCompare::EQUAL, 'password_new_bis', 
                                      array(), array('invalid' => 'The two passwords must be the same.')),
        new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::NOT_EQUAL, 'password_new', 
                                      array(), array('invalid' => "Current and new password can't be the same")),
      )));
  }
}
