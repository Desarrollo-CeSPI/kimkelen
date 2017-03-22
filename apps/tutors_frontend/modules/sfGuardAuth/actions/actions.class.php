<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php
  require_once(sfConfig::get('sf_plugins_dir').'/sfGuardSecurePlugin/modules/sfGuardAuth/lib/BasesfGuardAuthActions.class.php');

  class sfGuardAuthActions extends BasesfGuardAuthActions
  {

	  public function executeLogin(sfWebRequest $request) {

		  $this->redirectIf($this->getUser()->isAuthenticated(), "@homepage");

		  if ($request->isMethod('post')) {
			  $this->form = new sfGuardUserEmailForm();
			  die('lala');
			  $this->form->bind($request->getParameter($this->form->getName()));
			  if ($this->form->isValid()) {
				  $this->facebookLogin($request, $this->form->getValue('email_address'));
			  }
		  } else {
			  $this->facebookLogin($request);
		  }
	  }

	  private function facebookLogin($request, $email = null) {
		  include(sfConfig::get('sf_root_dir') . '/lib/custom/facebook.php');

		  $facebook = new Facebook(array(
			  'appId' => sfConfig::get('app_facebook_api_id'),
			  'secret' => sfConfig::get('app_facebook_api_secret'),
		  ));

		  $fb_user = $facebook->getUser();
die(var_dump($fb_user));
		  if ($fb_user) {
			  try {
				  $fb_profile = $facebook->api('/me'); // Devuelve el Profile de facebook.

				  if ($email) {// Si existe $email quiere decir que el usuario ingreso un mail diferente al que posee en facebook.
					  $fb_profile['email'] = $email;
				  }
				  $guard_user = GuardUserSocialTable::getOrCreateGuardUserBySocialData($fb_profile);
				  if (!$guard_user) {
					  $this->form = new sfGuardUserEmailForm();
				  }
			  } catch (FacebookApiException $e) {
				  $guard_user = null;
			  }
		  }

		  if ((isset($guard_user)) && (!is_null($guard_user))) {
			  $this->getUser()->signin($guard_user, false);
			  $this->getUser()->setAttribute("id", $guard_user->getProfile()->getId());
			  $this->redirect('@homepage');
		  }
	  }

    public function executeSignin($request)
    {
      $this->setLayout('cleanLayout');
      $user = $this->getUser();
      if ($user->isAuthenticated())
      {
        return $this->redirect('@homepage');
      }

      $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin');
      $this->form = new $class();

      if ($request->isMethod('post'))
      {
        $this->form->bind($request->getParameter('signin'));
        $values = $this->form->getValues();

			if ($this->form->isValid())
			{  
			  $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);

				$signinUrl = sfConfig::get('app_sf_guard_plugin_success_signin_url', $user->getReferer('@homepage'));

			  return $this->redirect($signinUrl);
			}

        
      }
		$this->setTemplate('signinFrontend');
    }
  }