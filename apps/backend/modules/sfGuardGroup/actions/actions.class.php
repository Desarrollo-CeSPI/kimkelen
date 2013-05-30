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
require_once(sfConfig::get('sf_plugins_dir').'/sfGuardSecurePlugin/modules/sfGuardGroup/lib/BasesfGuardGroupActions.class.php');

class sfGuardGroupActions extends BasesfGuardGroupActions
{

  public function executeDelete(sfWebRequest $request)
  {

    if(!sfGuardGroupPeer::canBeDeleted($request->getParameter('id')))
    {
      $this->getUser()->setFlash('warning','No se puede borrar el grupo seleccionado. Es un grupo definido por el sistema.');
      $this->redirect('@sf_guard_group');
    }

    return parent::executeDelete($request);
  }

  public function setDeleteFlash()
  {
    $this->getUser()->setFlash('notice', 'El grupo de usuarios fue eliminado correctamente.');
  }

  public function getProcessFormNotice($new)
  {
    return $new ? 'El grupo de usuarios fue creado correctamente.' : 'El grupo de usuarios fue actualizado correctamente.';
  }

  public function setProcessFormErrorFlash()
  {
    $this->getUser()->setFlash('error', 'El grupo de usuarios no fue guardado debido a algunos errores.', false);
  }

  public function setProcessFormSaveAndAddFlash($notice)
  {
    $this->getUser()->setFlash('notice', $notice.' Puede agregar otro más abajo.');
  }
}