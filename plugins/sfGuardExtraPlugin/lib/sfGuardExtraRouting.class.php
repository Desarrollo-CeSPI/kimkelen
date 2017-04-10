<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Gordon Franke <gfranke@savedcite.com>
 * @version    SVN: $Id: sfGuardExtraRouting.class.php 31704 2010-12-21 14:29:12Z garak $
 */
class sfGuardExtraRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r              = $event->getSubject();
    $enabledModules = sfConfig::get('sf_enabled_modules', array());

    // forgot password
    if (in_array('sfGuardForgotPassword', $enabledModules))
    {
      $r->prependRoute('sf_guard_user_set_password', new sfRoute('/set_password', array('module' => 'sfGuardForgotPassword', 'action' => 'userResetPassword')));
      $r->prependRoute('sf_guard_password', new sfRoute('/request_password', array('module' => 'sfGuardForgotPassword', 'action' => 'password')));
      $r->prependRoute('sf_guard_do_password', new sfRoute('/request_password/do', array('module' => 'sfGuardForgotPassword', 'action' => 'request_reset_password')));
      $r->prependRoute('sf_guard_forgot_password_reset_password', new sfRoute('/reset_password/:key/:id', array('module' => 'sfGuardForgotPassword', 'action' => 'reset_password')));
    }

    // register
    if (in_array('sfGuardRegister', $enabledModules))
    {
      $r->prependRoute('sf_guard_register', new sfRoute('/register', array('module' => 'sfGuardRegister', 'action' => 'register')));
      $r->prependRoute('sf_guard_do_register', new sfRoute('/register/do', array('module' => 'sfGuardRegister', 'action' => 'request_confirm_register')));
      $r->prependRoute('sf_guard_register_confirm', new sfRoute('/register/confirm/:key/:id', array('module' => 'sfGuardRegister', 'action' => 'register_confirm')));
      $r->prependRoute('sf_guard_register_complete', new sfRoute('/register/complete/:id', array('module' => 'sfGuardRegister', 'action' => 'register_complete')));
    }
  }
}
