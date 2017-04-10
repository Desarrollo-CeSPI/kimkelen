<?php

class dcMailer
{
	/**
	 * Send an email to users that want to reset their password.
	 *
	 * @param Request $request
	 */
	public static function sendResetPasswordEmail($person, $user, $link)
	{
		sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N", "Partial"));

		try {

			$to_name = $person->getFirstname();
			$body = 'Hola, '. $to_name . "\nSi usted solicitó resetear su contraseña, copie y pegue la siguiente dirección en su navegador:\n . $link. \nSi usted no ha solicitado el cambio, por favor desestime este correo.\n Muchas gracias";
			$from = sfConfig::get('app_sf_guard_extra_plugin_mail_from');
			$from_name = sfConfig::get('app_sf_guard_extra_plugin_name_from') . " | " . SchoolBehaviourFactory::getInstance()->getSchoolName();
			$to = $user->getEmail();
			$subject = sfConfig::get('app_sf_guard_extra_plugin_subject_request');

			$mailer = sfContext::getInstance()->getMailer();
			$message = Swift_Message::newInstance()
				->setFrom($from, $from_name)
				->setTo($to, $to_name)
				->setSubject($subject)
				->setBody($body);

			$message->setContentType("text/html");
			$res = $mailer->send($message);



		} catch (Exception $e) {
			sfContext::getInstance()->getLogger()->err('Se produjo un error enviando mail a los siguientes destinatarios ' . implode(", ", $to) . ' debido a: ' . $e->getMessage());
		}
	}
}