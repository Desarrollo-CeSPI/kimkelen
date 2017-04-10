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
 * @version    SVN: $Id: BasesfGuardRegisterActions.class.php 31653 2010-12-10 14:03:38Z garak $
 */
class BasesfGuardRegisterActions extends sfActions
{
  /**
   * preExecute
   *
   * @access public
   * @return void
   */
  public function preExecute()
  {
    if($this->getUser()->isAuthenticated())
    {
      $this->redirect('@homepage');
    }
  }

  /**
   * executeRegister
   *
   * @access public
   * @return void
   */
  public function executeRegister(sfWebRequest $request)
  {
    $this->form = new sfGuardFormRegister();

    if ($request->isMethod(sfRequest::POST))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();

        $sfGuardUser = new sfGuardUser();
        $sfGuardUser->fromArray($values, BasePeer::TYPE_FIELDNAME);
        if (isset($values['email']))
        {
          $sfGuardUser->setEmail($values['email']);
        }
        $sfGuardUser->setIsActive(false);
        $sfGuardUser->save();

        $messageParams = array(
          'sfGuardUser' => $sfGuardUser,
          'password' => $values['password']
        );
        $body = $this->getComponent($this->getModuleName(), 'send_request_confirm', $messageParams);
        $from = sfConfig::get('app_sf_guard_extra_plugin_mail_from', 'noreply@example.org');
        $fromName = sfConfig::get('app_sf_guard_extra_plugin_name_from', 'noreply');
        $to = $sfGuardUser->getEmail();
        $toName = $sfGuardUser->getUsername();
        $subject = sfConfig::get('app_sf_guard_extra_plugin_subject_confirm', 'Confirm Registration');
        $mailer = $this->getMailer();
        $message = $mailer->compose(array($from => $fromName), array($to => $toName), $subject, $body);
        $mailer->send($message);

        $this->getUser()->setFlash('values', $values);
        $this->getUser()->setFlash('sfGuardUser', $sfGuardUser);

        return $this->redirect('@sf_guard_do_register');
      }
    }
  }

  /**
   * executeRequest_confirm_register
   *
   * @access public
   * @return void
   */
  public function executeRequest_confirm_register(sfWebRequest $request)
  {
  }

  /**
   * executeRegister_confirm
   *
   * @access public
   * @return void
   */
  public function executeRegister_confirm(sfWebRequest $request)
  {
    $c = new Criteria();
    $c->add(sfGuardUserPeer::PASSWORD, $request->getParameter('key'));
    $c->add(sfGuardUserPeer::ID, $request->getParameter('id'));
 	  $sfGuardUser = sfGuardUserPeer::doSelectOne($c);
    $this->forward404Unless($sfGuardUser, 'user not found');
    $sfGuardUser->setIsActive(true);
    $sfGuardUser->save();

    $messageParams = array(
      'sfGuardUser' => $sfGuardUser,
    );
    $body = $this->getComponent($this->getModuleName(), 'send_complete', $messageParams);
    $from = sfConfig::get('app_sf_guard_extra_plugin_mail_from', 'noreply@example.org');
    $fromName = sfConfig::get('app_sf_guard_extra_plugin_name_from', 'noreply');
    $to = $sfGuardUser->getEmail();
    $toName = $sfGuardUser->getUsername();
    $subject = sfConfig::get('app_sf_guard_extra_plugin_subject_complete', 'Request complete');
    $mailer = $this->getMailer();
    $message = $mailer->compose(array($from => $fromName), array($to => $toName), $subject, $body);
    $mailer->send($message);

    $this->getUser()->signin($sfGuardUser);

    $this->redirect('@sf_guard_register_complete?id='.$sfGuardUser->getId());
  }

  /**
   * executeRegister_complete
   *
   * @access public
   * @return void
   */
  public function executeRegister_complete(sfWebRequest $request)
  {
  }
}
