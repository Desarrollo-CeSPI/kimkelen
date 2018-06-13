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

		if ($request->isMethod(sfRequest::POST)) {
			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()) {
				$values = $this->form->getValues();

				$sf_guard_user = sfGuardUserPeer::retrieveByUsernameOrEmail($values['username_or_email'], true);
				$tutor = TutorPeer::retrieveByUsername($sf_guard_user->getUsername());

				if (!is_null($tutor)) {

					$token_user = new TokenUser();
					$token_user->setsfGuardUser($sf_guard_user);
					$token_user->setToken(md5(uniqid(rand(), true)));
					$token_user->save();


					$result = dcMailer::sendResetPasswordEmail($tutor->getPerson(), $token_user);

					if ($result) {
						$this->redirect('@request_reset_password');
					}
				}
			}
		}
			$this->setLayout('cleanLayout');

	}

	public function executeRequestResetPassword(){
		$this->setLayout('cleanLayout');
  }

	/**
	 * Redirects the user to change password form only if token is valid.
	 * @param sfRequest $request
	 */
	public function executeResetPassword($request)
	{
		$this->token = $request->getParameter('token');
		$token_user = TokenUserPeer::retrieveByToken($this->token);

		if (!is_null($token_user)) {
			$sf_guard_user = sfGuardUserPeer::retrieveByPK($token_user->getsfGuardUserId());
			$this->form = new sfGuardFormResetPassword(null, array('userid' => $sf_guard_user->getId()));

			if ($request->isMethod(sfRequest::POST) && $this->form->bindAndSave($request->getParameter($this->form->getName()))) {
				$this->getUser()->setFlash('notice', "La contraseña fue restablecida satisfactoriamente.");
				$this->redirect('@sf_guard_signin');
			}

			$this->setLayout('cleanLayout');
			$this->setTemplate('askPassword');

		} else {
			$this->setLayout('cleanLayout');
			$this->setTemplate('invalidKey');
		}

	}

}