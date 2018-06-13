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
    public function executeSignin($request)
    {
      // Acá habría que chequear que si no es un tutor, no se pueda loguear.

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
        
        $tutor=TutorPeer::retrieveByUsername($values['user']);
        
        if(!is_null($tutor) && $tutor->getPerson()->getIsActive())
        {
            if ($this->form->isValid())
            {  
				
		$this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);
		//tiene facebookID lo asocio al usuario.
		if(!is_null($user->getFacebookId()))
		{
                    $social_user = new GuardUserSocial();
                    $social_user->setSocialId($user->getFacebookId());
                    $social_user->setUserId($tutor->getPerson()->getUserId());
                    $social_user->save();
		}
		$signinUrl = sfConfig::get('app_sf_guard_plugin_success_signin_url', $user->getReferer('@homepage'));

		 return $this->redirect($signinUrl);
            }
	   }
        else{
            if(!is_null($tutor) && !$tutor->getPerson()->getIsActive()){
                $this->getUser()->resetFacebookAttributes();
                $this->getUser()->setFlash('notice', "La cuenta de usuario se encuentra deshabilitada.");
                $this->redirect('@sf_guard_signin');
            }        
        }
        
      }
        $this->setTemplate('signinFrontend');
    }
    
    public function executeFacebookLogin($request)
    {
	    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
	    $my_url = url_for('@facebook_login', true);
	    $code = $request->getParameter('code');
	
	    $app_id = sfConfig::get("app_facebook_app_id");
	    $app_secret = sfConfig::get("app_facebook_app_secret");
       
      if (empty($code))
      {
          //no viene el codigo como parametro. Creo un codigo state
          $state = md5(uniqid(rand(), TRUE)); //CSRF protection
          $this->getUser()->setFacebookState($state);

          $dialog_url = "https://www.facebook.com/dialog/oauth?client_id="
                  . $app_id . "&redirect_uri=" . $my_url . "&state=" . $state;

          $this->redirect($dialog_url);
      }
        
      if (!empty($code) && $this->getUser()->getFacebookState() === $request->getParameter('state'))
      {
        $token_url = "https://graph.facebook.com/v2.8/oauth/access_token?"
                . "client_id=" . $app_id . "&redirect_uri=" . $my_url
                . "&client_secret=" . $app_secret . "&code=" . $code;

        $response = file_get_contents($token_url);

        $params = null;
        parse_str($response, $params);

        $response = json_decode($response);

        $graph_url = "https://graph.facebook.com/me?access_token="
                . $response->access_token;

        $facebook_user = json_decode(file_get_contents($graph_url));

        if ($facebook_user->id)
        {
          $user = GuardUserSocialPeer::retrieveBySocialId($facebook_user->id);

          $this->getUser()->setFacebookId($facebook_user->id);
          $this->getUser()->setFacebookName($facebook_user->name);

          if (!is_null($user))
          {
              // si ya estaba asociado a un usuario y HABILITADO  lo ingreso a la cuenta de kimkelen
              $user_app = sfGuardUserPeer::retrieveByPk($user->getUserId());
              $tutor= TutorPeer::retrieveByUsername($user_app->getUsername());
              if($tutor->getPerson()->getIsActive())
              {
                $this->getUser()->signin($user_app, false);
                $this->redirect('@homepage');   
              }
              else
              {
                  $this->getUser()->resetFacebookAttributes();
                  $this->getUser()->setFlash('notice', "La cuenta de usuario se encuentra deshabilitada.");
                  $this->redirect('@sf_guard_signin');
              }
          }
          else
          {
            // new user
            if (!$this->getUser()->isAuthenticated())
            {
                $this->getUser()->setFlash('notice', "Tu cuenta de Facebook no está asociada a Kimkëlen, ingresá con tu usuario y contraseña y luego podrás ingresar con Facebook.");
                $this->redirect('@sf_guard_signin');

            }
          }
        }
      }
		
        $this->redirect('@sf_guard_signin');
		
    }
	
    public function executeSignout($request)
    {
	$this->getUser()->resetFacebookAttributes();
	$this->getUser()->signOut();

	$signoutUrl = sfConfig::get('app_sf_guard_plugin_success_signout_url', $request->getReferer());

	$this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
    }
    
    public function executeFacebookUnlink($request)
    {
        //delete
        GuardUserSocialPeer::deleteBySocialId($this->getUser()->getFacebookId());
        $this->getUser()->resetFacebookAttributes();
        $this->redirect('@homepage');
        
    }
	
  }
