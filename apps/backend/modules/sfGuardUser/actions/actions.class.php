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
require_once(sfConfig::get('sf_plugins_dir').'/sfGuardSecurePlugin/modules/sfGuardUser/lib/BasesfGuardUserActions.class.php');
  
class sfGuardUserActions extends BasesfGuardUserActions
{
  /**
   * This action (de)activates person and relative guard_user to that person
   *
   * @param sfWebRequest $request
   */
  public function executeActivation(sfWebRequest $request)
  {
    $this->guard_user = $this->getRoute()->getObject();
    $this->guard_user->activation();    
    $this->getUser()->setFlash('info','The item was updated successfully.');
    $this->redirect('@sf_guard_user');
  }

  public function setDeleteFlash()
  {
    $this->getUser()->setFlash('notice', 'El usuario fue eliminado correctamente.');
  }

  public function getProcessFormNotice($new)
  {
    return $new ? 'El usuario fue creado correctamente.' : 'El usuario fue actualizado correctamente.';
  }

  public function setProcessFormErrorFlash()
  {
    $this->getUser()->setFlash('error', 'El usuario no fue guardado debido a algunos errores.', false);
  }

  public function setProcessFormSaveAndAddFlash($notice)
  {
    $this->getUser()->setFlash('notice', $notice.' Puede agregar otro más abajo.');
  }
}