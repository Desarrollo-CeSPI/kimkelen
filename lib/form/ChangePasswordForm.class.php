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

class ChangePasswordForm extends sfGuardUserForm
{
  public function removeFields()
  {
    unset(
      $this['algorithm'],
      $this['salt'],
      $this['created_at'],
      $this['last_login'],
      $this['is_active'],
      $this['username'],
      $this['is_super_admin'],
      $this['sf_guard_user_group_list'],
      $this['sf_guard_user_permission_list'],
      $this['project_evaluator_list'],
      $this['teacher_guard_user_list'],
      $this['student_guard_user_list']
    );
  }

  public function configure()
  {
    $this->removeFields();
    
    //Widgets
    $this->widgetSchema['id']= new sfWidgetFormInputHidden();
    $this->widgetSchema['actual_password'] = new sfWidgetFormInputPassword();
    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->widgetSchema['confirm_password'] = new sfWidgetFormInputPassword();
    $this->widgetSchema->moveField('actual_password',sfWidgetFormSchema::BEFORE,'password');
    $this->widgetSchema->setHelp('password',"Debe tener al menos 8 caracteres y debe contener por lo menos 2 caracteres alfabéticos, dos digitos y un símbolo entre !@#$%^&*");

    $this->widgetSchema->setLabels(array("actual_password" => "Contraseña actual",
                                         "password" => "Nueva contraseña",
                                         "confirm_password" => "Repetir contraseña nueva"));

    //validators
    $this->widgetSchema->setNameFormat("changepassword[%s]");
    $this->validatorSchema['actual_password'] = new sfValidatorString(array('required' => true));
    $this->validatorSchema['password'] = new sfGuardSecurePasswordValidator(array('required' => true));
    $this->validatorSchema['confirm_password'] = new sfGuardSecurePasswordValidator(array('required' => $this->isNew()));


    $this->validatorSchema->setOption("allow_extra_fields", true);
  }
}