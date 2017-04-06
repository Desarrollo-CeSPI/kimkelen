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

/**
 * Description of sfGuardUser
 *
 * @author gramirez
 */
class sfGuardUser extends PluginsfGuardUser
{

  public function canActive()
  {
    $user = sfContext::getInstance()->getUser();
    return !$this->getIsActive() && $user->isSuperAdmin();

  }

  public function canInactive()
  {
    $user = sfContext::getInstance()->getUser();

    return $this->getIsActive() && $user->isSuperAdmin();

  }

  public function activation()
  {
    $this->setIsActive(!$this->getIsActive());
    $this->save();

  }

  public function canEdit()
  {
    return !($this->hasGroup("Jefe de preceptores") || $this->hasGroup("Profesor") || $this->hasGroup('Preceptor'));

  }

  public function getMessageCantEdit()
  {
    return 'El usuario pertenece a los grupos Profesor o Preceptor, debe editarlos desde sus respectivos listados';
  }

  public function getEmail() {
    return $this->getProfile()->getEmail();
  }
}