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

class ChangeRoleForm extends sfForm
{
  public function configure()
  {
    $choices = array("" => "");
    foreach (sfGuardUserGroupPeer::retrieveForUserWithoutCurrentRole($this->getOption('actual_user')->getLoginRole(), $this->getOption('actual_user')->getGuardUser()->getId()) as $user_role)
      $choices[$user_role->getId()] = $user_role;

    $this->setWidget('roles', new sfWidgetFormChoice(array(
        'choices' => $choices
      )));
    $this->validatorSchema['roles'] = new sfValidatorChoice(array('choices' => array_keys($choices)));
    $this->widgetSchema['roles']->setLabel('Change role: ');
    $this->getWidgetSchema()->setNameFormat('user_role[%s]');
    $this->getValidatorSchema()->setOption('allow_extra_fields', true);
  }

}