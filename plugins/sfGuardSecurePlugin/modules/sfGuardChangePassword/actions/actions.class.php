<?php

/**
 * sfGuardChangePassword actions.
 *
 * @package    csGuardExtended
 * @subpackage sfGuardChangePassword
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class sfGuardChangePasswordActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $user = $this->getUser();
    $class = sfConfig::get('app_sf_guard_plugin_change_password_form', 'sfGuardChangePasswordForm');
    $this->form = new $class();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('change_password'));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();
        $this->getUser()->changePassword($values['password_new']);
        $this->getUser()->setFlash('notice','Password changed successfuly');
        return $this->redirect('@homepage');
      }
    }
  }
}
