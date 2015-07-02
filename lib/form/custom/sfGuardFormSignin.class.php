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

class sfGuardFormSignin extends sfGuardSecureFormSignin
{
  public function configure()
  {
    parent::configure();
    unset ($this['remember']);
    $this->setWidget('username', new sfWidgetFormInput());
    $this->setWidget('password', new sfWidgetFormInput(array('type' => 'password')));
    #$this->setWidget('remember', new sfWidgetFormInputCheckbox());

    $this->setValidator('username', new sfValidatorString());
    $this->setValidator('password', new sfValidatorString());
    #$this->setValidator('remember', new sfValidatorBoolean());

    $this->getValidatorSchema()->setPostValidator(new sfGuardValidatorUser());

    $this->getWidgetSchema()->setNameFormat('signin[%s]');

    if ( isset ($this['captcha']) )
    {
      $this->getWidget('captcha')->setLabel('Enter the digits here:');
      $this->getWidgetSchema()->moveField('captcha', 'after', 'password');
    }

  }
}