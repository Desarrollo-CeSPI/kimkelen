<?php

class dcMailer
{
	/**
	 * Send an email to users that want to reset their password.
	 *
	 * @param Request $request
	 */
	public static function sendResetPasswordEmail($person, $sf_guard_user)
	{
		sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N", "Partial"));

		try {

			$to_name = $person->getFirstname();
			$body = 'Hola, '. $to_name .". Si usted solicitó resetear su contraseña, haga click en el siguiente link. Si usted no ha solicitado el cambio, por favor desestime este correo.";
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

		} catch (Exception $e) {
			sfContext::getInstance()->getLogger()->err('Se produjo un error enviando mail a los siguientes destinatarios ' . implode(", ", $to) . ' debido a: ' . $e->getMessage());
		}
	}
}