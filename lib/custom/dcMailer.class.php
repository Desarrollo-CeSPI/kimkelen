<?php

class dcMailer
{
	/**
	 * Send an email to users that want to reset their password.
	 *
	 * @param Request $request
	 */
	public static function sendResetPasswordEmail($person, $token_user)
	{
		sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N", "Partial"));

		try {

			$from = sfConfig::get('app_sf_guard_extra_plugin_mail_from');
			$from_name = sfConfig::get('app_sf_guard_extra_plugin_name_from') . " | " . SchoolBehaviourFactory::getInstance()->getSchoolName();
			$to = $token_user->getsfGuardUser()->getEmail();
			$subject = sfConfig::get('app_sf_guard_extra_plugin_subject_request');

			$mailer = sfContext::getInstance()->getMailer();
			$message = Swift_Message::newInstance()
				->setFrom($from, $from_name)
				->setTo($to, $person->getFullname())
				->setSubject($subject)
				->setBody(get_partial("mailer/reset_password", array("person" => $person, "token" => $token_user->getToken())));

			$message->setContentType("text/html");
			$res = $mailer->send($message);
			return $res;

		} catch (Exception $e) {
			sfContext::getInstance()->getLogger()->err('Se produjo un error enviando mail a los siguientes destinatarios ' . implode(", ", $to) . ' debido a: ' . $e->getMessage());
		}
	}
}