<?php

class PluginsfGuardLoginFailure extends BasesfGuardLoginFailure
{

  public static function trackFailure($username)
  {
    $failure = new sfGuardLoginFailure();
    $failure->setUsername($username);
    $failure->setFailedAt(time());
    $failure->setCookieId(array_key_exists('HTTP_COOKIE', $_SERVER) ? $_SERVER['HTTP_COOKIE']: null);
    $failure->setIpAddress(array_key_exists('REMOTE_ADDR', $_SERVER)? $_SERVER['REMOTE_ADDR']: null);
    $failure->save();
    if ( $context = sfContext::getInstance())
    {
      $context->getEventDispatcher()->notify(new sfEvent('sfGuardSecurePlugin', 'application.log', array(
      'message'        => sprintf ("Login failed for user=%s ip=%s", $failure->getUsername(), $failure->getIpAddress()),
      'priority'       => sfLogger::ERR,
    )));
    }
  }
}
