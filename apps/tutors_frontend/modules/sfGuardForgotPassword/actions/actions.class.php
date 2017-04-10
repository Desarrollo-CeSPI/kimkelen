<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../lib/BasesfGuardForgotPasswordActions.class.php');

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 7634 2008-02-27 18:01:40Z fabien $
 */
class sfGuardForgotPasswordActions extends BasesfGuardForgotPasswordActions
{
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

				$sf_guard_user = sfGuardUserPeer::retrieveByUsernameOrEmail($values['username_or_email'], true);
				$tutor = TutorPeer::retrieveByUsername($sf_guard_user->getUsername());

				$this->forward404Unless($tutor, 'user tutor not found');


				$token_user = new TokenUser();
				$token_user->setsfGuardUser($sf_guard_user);
				$token_user->setToken(md5(uniqid(rand(), true)));
				$token_user->save();
				$link = $this->generateUrl('reset_password', array(), true) . "/" . $token_user->getToken();
				dcMailer::sendResetPasswordEmail($tutor->getPerson(), $token_user->getsfGuardUser(), $link);

				$this->setTemplate('request_reset_password');
			}
		}

		$this->setLayout('cleanLayout');
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
}
