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
 * TeacherCustom form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class TeacherCustomForm extends TeacherForm
{
  public function configure()
  {
    parent::configure();
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    
    unset ($this['person-lastname'],
           $this['person-firstname'],
           $this['person-identification_type'],
           $this['person-identification_number'],
           $this['person-sex'],
           $this['person-cuil'],
           $this['person-birthdate'],
           $this['person-birth_country'],
           $this['person-birth_state'],
           $this['person-birth_department'],
           $this['person-birth_city'],
           $this['person-photo'],
           $this['person-observations'],
           $this['person-email'],
           $this['person-phone'],
           $this['person-address'],
           $this['person-nationality_id'],
           $this['examination_repproved_subject_teacher_list'],
           $this['examination_subject_teacher_list'],
           $this['salary']
    );
   
  
    $this->setWidget('person-username', new sfWidgetFormInput());
    $this->setValidator('person-username', new sfValidatorString(array('min_length' => 4, 'max_length' => 128, 'required' => true),array(
        'min_length' => __('Username must be at least 4 characters long'),
        'max_length' => __('Username must be at most 128 characters long')
    )));
    $this->getWidgetSchema()->setHelp('person-username',__('if blank no username will be assigned'));
    
    $this->setWidget('person-password', new sfWidgetFormInputPassword());
    $this->setWidget('person-password_again',new sfWidgetFormInputPassword());

    $this->setValidator('person-password', new sfGuardSecurePasswordValidator(array('required' => true)));
    $this->setValidator('person-password_again', new sfGuardSecurePasswordValidator(array('required' => true)));
           
        
    $this->getWidgetSchema()->setLabel('person-username', 'Username');
    $this->getWidgetSchema()->setLabel('person-password', 'Password');
    $this->getWidgetSchema()->setLabel('person-password_again', 'Password again');
 
  }
  
  
  protected function doSave($con = null)
  {

    BaseTeacherForm::doSave($con);
    $guard_user = $this->getObject()->getPersonSfGuardUser();
    if ( !is_null($guard_user))
    {   $teacher_group = BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::TEACHER);
        if ( ! array_key_exists( $teacher_group,$guard_user->getGroups()) )
        {
          $guard_user->addGroupByName($teacher_group);
          $guard_user->save($con);
        }
    }
    
  }

  


}
