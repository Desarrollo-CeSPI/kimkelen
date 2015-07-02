<?php

class sfGuardSecureFormSignin extends sfForm
{
   public function configure()
   {
    if ( $this->checkSecurityAttack() )
    {
      $this->setWidget('captcha', new sfWidgetCaptchaGD());
      $this->setValidator('captcha', new sfCaptchaGDValidator(array('length'=>4)));
    }
   }

    protected function checkSecurityAttack()
    {
        $user = sfContext::getInstance()->getUser();
        if ( $user->getAttribute('sf_guard_secure_plugin_login_failure_detected',0) ) return true;

        $login_failure_max_attempts_per_ip   = sfConfig::get('app_sf_guard_secure_plugin_login_failure_max_attempts_per_ip', 10);    
        $login_failure_time_window           = sfConfig::get('app_sf_guard_secure_plugin_login_failure_time_window', 90); 
  
        $failures_for_ip   = sfGuardLoginFailurePeer::doCountForIpInTimeWindow($_SERVER['REMOTE_ADDR'] , $login_failure_time_window*60 );


        if ( $failures_for_ip > $login_failure_max_attempts_per_ip )
        {
          $user->setAttribute('sf_guard_secure_plugin_login_failure_detected', 1);
        }

        return $user->getAttribute('sf_guard_secure_plugin_login_failure_detected',0);
    }

}
