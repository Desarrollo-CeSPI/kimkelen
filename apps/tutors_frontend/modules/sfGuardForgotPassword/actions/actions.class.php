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
				$this->forward404Unless($sf_guard_user, 'user not found');

				$to_name = $sf_guard_user->getUsername();
				$body = 'Hola,';//. $to_name . ". Si usted solicitó resetear su contraseña, haga click en el siguiente link. Si no ha sido usted, desestime este correo.";
				$from = 'no-responder@kimkelen.com';
				$from_name = sfConfig::get('app_sf_guard_extra_plugin_name_from');
				$to = $sf_guard_user->getEmail();
				$subject = "Reseteo";//sfConfig::get('app_sf_guard_extra_plugin_subject_request');


				$mailer = sfContext::getInstance()->getMailer();
				$message = Swift_Message::newInstance()
					->setFrom($from)
					->setBcc($to)
					->setSubject($subject)
					->setBody($body);

				$message->setContentType("text/html");
				$mailer->send($message);
			}

		}

		$this->setLayout('cleanLayout');
	}
}
