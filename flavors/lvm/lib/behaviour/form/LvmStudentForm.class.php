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
 * extends StudentForm
 *
 * @author gramirez
 */
class LvmStudentForm extends StudentForm
{

  public function configure ()
  {
    parent::configure();
    $this->getValidator('global_file_number')->setOption('required', false);
  }

  public function unsetFields()
  {
    unset($this['person_id']);
  }

  public function getFormFieldsDisplay()
  {
    $personal_data_fields = array('person-lastname', 'person-firstname', 'person-identification_type', 'person-identification_number','origin_school_id', 'global_file_number','person-sex', 'person-cuil', 'person-birthdate', 'person-birth_country', 'person-birth_state','person-birth_department' ,'person-birth_city', 'person-photo', 'person-observations' );
    if($this->getObject()->getPerson()->getPhoto())
    {
      $personal_data_fields = array_merge($personal_data_fields, array('person-current_photo', 'person-delete_photo'));
    }
    return array(
          'Personal data'   =>  $personal_data_fields,
          'Contact data'    =>  array('person-email', 'person-phone', 'person-address'),
          'Health data'   =>  array('blood_group', 'blood_factor', 'health_coverage_id', 'emergency_information'),
          //'System access'   =>  array('person-username', 'person-password', 'person-password_again' ),
          'Tags' => array('student_tag_list'),
//          'Work data'       =>  array('occupation_id', 'busy_starts_at', 'busy_ends_at'),
    );
  }

}
