<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Gordon Franke <gfranke@savedcite.com>
 * @version    SVN: $Id: BasesfGuardForgotPasswordActions.class.php 31720 2010-12-23 09:18:11Z garak $
 */
class BasesfGuardForgotPasswordActions extends sfActions
{
  /**
   * preExecute
   *
   * @access public
   * @return void
   */
  public function preExecute()
  {
    if ($this->getUser()->isAuthenticated())
    {
      $this->redirect('@homepage');
    }
  }

  /**
   * executePassword
   *
   * Form for requesting instructions on how to reset your password
   *
   * @param  sfRequest $request
   * @return void
   * @author Jonathan H. Wage
   */
  public function executePassword(sfWebRequest $request)
  {
    $this->form = new sfGuardFormForgotPassword();

    if ($request->isMethod(sfRequest::POST))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();

        $sfGuardUser = sfGuardUserPeer::retrieveByUsernameOrEmail($values['username_or_email'], true);
        $this->forward404Unless($sfGuardUser, 'user not found');

        $messageParams = array(
          'sfGuardUser' => $sfGuardUser,
        );
        $body = $this->getComponent($this->getModuleName(), 'send_request_reset_password', $messageParams);
        $from = sfConfig::get('app_sf_guard_extra_plugin_mail_from', 'noreply@example.org');
        $fromName = sfConfig::get('app_sf_guard_extra_plugin_name_from', 'noreply');
        $to = $sfGuardUser->getEmail();
        $toName = $sfGuardUser->getUsername();
        $subject = sfConfig::get('app_sf_guard_extra_plugin_subject_request', 'Request to reset password');
        $mailer = $this->getMailer();
        $message = $mailer->compose(array($from => $fromName), array($to => $toName), $subject, $body);
        $mailer->send($message);

        return $this->redirect('@sf_guard_do_password?'.http_build_query($values));
      }
    }

  }

  /**
   * executeRequest_reset_password
   *
   * @access public
   * @param  sfRequest $request
   * @return void
   */
  public function executeRequest_reset_password(sfWebRequest $request)
  {
  }

  /**
   * executeReset_password
   *
   * Reset the users password and e-mail it
   *  OR display a form to let user choose a new password
   *
   * @access public
   * @param  sfRequest $request
   * @return void
   */
  public function executeReset_password(sfWebRequest $request)
  {
    if (sfConfig::get('app_sf_guard_extra_plugin_reset_type', 'set') == 'set')
    {
      $this->setPassword($request);
    }
    else
    {
      $this->askPassword($request);
    }
  }

  /**
   * executeUserResetpassword
   *
   * Reset password after user has been asked for
   *
   * @access public
   * @param  sfRequest $request
   * @return void
   */
  public function executeUserResetPassword(sfWebRequest $request)
  {
    $this->askPassword($request);
  }

  /**
   * executeInvalid_key
   *
   * @access public
   * @param  sfRequest $request
   * @return void
   */
  public function executeInvalid_key(sfWebRequest $request)
  {
  }

  /**
   * Reset the users password and e-mail it
   * @param sfRequest $request
   */
  protected function setPassword(sfWebRequest $request)
  {
    $c = new Criteria();
    $c->add(sfGuardUserPeer::PASSWORD, $request->getParameter('key'));
    $c->add(sfGuardUserPeer::ID, $request->getParameter('id'));
 	  $sfGuardUser = sfGuardUserPeer::doSelectOne($c);
    $this->forwardUnless($sfGuardUser, 'sfGuardForgotPassword', 'invalid_key');

    $newPassword = time();
    $sfGuardUser->setPassword($newPassword);
    $sfGuardUser->save();

    $messageParams = array(
      'sfGuardUser' => $sfGuardUser,
      'password'    => $newPassword,
    );
    $body = $this->getComponent($this->getModuleName(), 'send_reset_password', $messageParams);
    $from = sfConfig::get('app_sf_guard_extra_plugin_mail_from', 'noreply@example.org');
    $fromName = sfConfig::get('app_sf_guard_extra_plugin_name_from', 'noreply');
    $to = $sfGuardUser->getEmail();
    $toName = $sfGuardUser->getUsername();
    $subject = sfConfig::get('app_sf_guard_extra_plugin_subject_success', 'Password reset successfully');
    $mailer = $this->getMailer();
    $message = $mailer->compose(array($from => $fromName), array($to => $toName), $subject, $body);
    $mailer->send($message);
  }

  /**
   * Ask user a new password and change it
   * @param sfRequest $request
   */
  protected function askPassword(sfWebRequest $request)
  {
    if (!is_null($key = $request->getParameter('key')))
    {
      $c = new Criteria();
      $c->add(sfGuardUserPeer::ID, $request->getParameter('id'));
      $c->add(sfGuardUserPeer::PASSWORD, $key);
      $user = sfGuardUserPeer::doSelectOne($c);
      $this->forward404Unless($user, 'user not found');
      $this->getUser()->setAttribute('userid', $user->getId());
    }
    $this->form = new sfGuardFormResetPassword(null, array('userid' => $this->getUser()->getAttribute('userid')));
    if ($request->isMethod(sfRequest::POST) && $this->form->bindAndSave($request->getParameter($this->form->getName())))
    {
      $this->redirect('@sf_guard_signin');
    }
    $this->setTemplate('ask_password');
  }

}
