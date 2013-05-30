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
        if ($this->form->isValid())
        {
          $values = $this->form->getValues();
          $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);

            $signinUrl = sfConfig::get('app_sf_guard_plugin_success_signin_url', $user->getReferer('@homepage'));

          return $this->redirect($signinUrl);
        }
      }
      else
      {
        if ($request->isXmlHttpRequest())
        {
          $this->getResponse()->setHeaderOnly(true);
      //    $this->getResponse()->setStatusCode(401);

          return sfView::NONE;
        }

        // if we have been forwarded, then the referer is the current URL
        // if not, this is the referer of the current request
        $user->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());

        $module = sfConfig::get('sf_login_module');
        if ($this->getModuleName() != $module)
        {
          return $this->redirect($module.'/'.sfConfig::get('sf_login_action'));
        }

        //$this->getResponse()->setStatusCode(401);
      }
    }
  }