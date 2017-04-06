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

				$to_name = $tutor->getPerson()->getFirstname();
				$body = 'Hola, '. $to_name .". Si usted solicitó resetear su contraseña, haga click en el siguiente link.\nSi usted no ha solicitado el cambio, por favor desestime este correo.";
				$from = sfConfig::get('app_sf_guard_extra_plugin_mail_from');
				$from_name = sfConfig::get('app_sf_guard_extra_plugin_name_from') . " | " . SchoolBehaviourFactory::getInstance()->getSchoolName();
				$to = $sf_guard_user->getEmail();
				$subject = sfConfig::get('app_sf_guard_extra_plugin_subject_request');

				$mailer = sfContext::getInstance()->getMailer();
				$message = Swift_Message::newInstance()
					->setFrom('no-responder@kimkelen.com', $from_name)
					->setTo($to, $to_name)
					->setSubject($subject)
					->setBody($body);

				$message->setContentType("text/html");

				$mailer->send($message);
			}

		}

		$this->setLayout('cleanLayout');
	}
}
