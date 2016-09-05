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
 * Description of StudentOfficePersonalForm
 *
 * @author gramirez
 */
class StudentOfficePersonalForm extends BasePersonalForm
{
  public function configure()
  {
    unset($this['person_id'], $this['personal_type']);

    $this->setWidget('aging_institution', new csWidgetFormDateInput(array('change_year' => true)));
    $this->setValidator('aging_institution', new mtValidatorDateString());

    $this->setValidator('salary', new sfValidatorNumber(array('required' => false)));

    //PERSON FORM
    $person = $this->getObject()->getPerson();
    if (is_null($person))
    {
      $person = new Person();
      $this->getObject()->setPerson($person);
    }
    
    $personForm = new PersonForm($person, array('related_class'=>'personal','embed_as'=>'person'));
    $this->embedMergeForm('person',$personForm);
  }

  public function getFormFieldsDisplay()
  {
    $personal_data_fields = array( 'person-lastname', 'person-firstname', 'person-identification_type', 'person-identification_number', 'person-sex', 'person-cuil', 'person-birthdate', 'person-birth_country', 'person-birth_state', 'person-birth_department', 'person-birth_city', 'person-photo', 'person-observations' );
    if($this->getObject()->getPerson()->getPhoto())
    {
      $personal_data_fields = array_merge($personal_data_fields, array('person-current_photo', 'person-delete_photo'));
    }

    return array(
      'Personal data'   =>  $personal_data_fields,
      'Contact data'   => array('person-email', 'person-phone', 'person-address'),
      'System access'  => array('person-username', 'person-password', 'person-password_again'),
      'Work data'      => array('file_number', 'salary', 'aging_institution'),
    );
  }

  protected function doSave($con = null)
  {
    $this->getObject()->setPersonalType(PersonalType::STUDENTS_OFFICE);
    parent::doSave($con);
    $guard_user = $this->getObject()->getPersonSfGuardUser();
    if ( !is_null($guard_user))
    {
      $personal_group =  BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::STUDENT_OFFICE);
      if ( ! array_key_exists( $personal_group,$guard_user->getGroups()) )
      {
        $guard_user->addGroupByName($personal_group);
        $guard_user->save($con);
      }
    }
    if(isset($values['person-photo']) && !$values['person-photo'])
    {
      $values = $this->getValues();
      if(isset($values['person-delete_photo']) && $values['person-delete_photo'])
      {
        $this->getObject()->getPerson()->deleteImage();
      }
    }
   }
}