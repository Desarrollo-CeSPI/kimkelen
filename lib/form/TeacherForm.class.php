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
 * Teacher form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class TeacherForm extends BaseTeacherForm
{
  public function configure()
  {
    unset($this['person_id']);
    $person = $this->getObject()->getPerson();
    if (is_null($person)) {
        $person = new Person();
        $this->getObject()->setPerson($person);
    }
    $personForm = new PersonForm($person, array('related_class'=>'teacher','embed_as'=>'person'));
    $this->embedMergeForm('person',$personForm);
    $this->setWidget('aging_institution', new csWidgetFormDateInput(array('change_year' => true, 'change_month' => true)));
    $this->getWidget('aging_institution')->setOption('year_range', (date('Y')-80).':'.(date('Y')));

    $this->setValidator('aging_institution', new mtValidatorDateString(array('required'=>false)));
    if ($this->getObject()->isNew()) {
      $this->getWidget('aging_institution')->setDefault(date('d/m/Y'));
    }

  }


  public function getFormFieldsDisplay()
  {
    $personal_data_fields = array( 'person-lastname', 'person-firstname', 'person-identification_type', 'person-identification_number', 'person-sex', 'person-cuil', 'person-birthdate', 'person-birth_country', 'person-birth_state', 'person-birth_department','person-birth_city', 'person-photo', 'person-observations' );
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

    parent::doSave($con);
    $guard_user = $this->getObject()->getPersonSfGuardUser();
    if ( !is_null($guard_user))
    {   $teacher_group = BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::TEACHER);
        if ( ! array_key_exists( $teacher_group,$guard_user->getGroups()) )
        {
          $guard_user->addGroupByName($teacher_group);
          $guard_user->save($con);
        }
    }
    $values = $this->getValues();
    if(is_null($values['person-photo']))
    {
      if(isset($values['person-delete_photo']) && $values['person-delete_photo'])
      {
        $this->getObject()->getPerson()->deleteImage();
      }
    }
  }


}
