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
 * sfGuardUser form.
 *
 * @package    form
 * @subpackage sf_guard_user
 * @version    SVN: $Id: sfGuardUserForm.class.php 13001 2008-11-14 10:45:32Z noel $
 */
class sfGuardUserCustomForm extends sfGuardUserAdminForm
{
  protected
    $pkName = null;

  public function configure()
  {
    parent::configure();

    $this->setWidget('sf_guard_user_group_list', new sfWidgetFormPropelChoice(array('model' => 'sfGuardGroup', 'peer_method' => 'retrieveGroups')));
    unset(
      $this['last_login'],
      $this['created_at'],
      $this['salt'],
      $this['algorithm'],
      $this['is_active'],
      $this['sf_guard_user_permission_list']
    );
    
    $this->validatorSchema['username'] = new sfValidatorRegex(array(
      'pattern' => '/^[a-z0-9_-]{3,16}$/'
    ), array(
      'invalid' => 'El nombre de usuario debe ser de entre 3 y 16 caracteres y contener letras, número o - y _'
    ));
  }
}