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
 * AgropecuariaTutorForm
 *
 * @author Desarrollo CeSPI
 */
class AgropecuariaTutorForm extends TutorForm
{
  public function configure()
  {
    unset($this['person_id']);
    $person = $this->getObject()->getPerson();
    if (is_null($person))
    {
      $person = new Person();
      $this->getObject()->setPerson($person);
    }
    $personForm = new PersonForm($person, array('related_class' => 'tutor', 'embed_as' => 'person'));
    $personForm->getValidator('phone')->setOption('required', true);
    $personForm->getValidator('birthdate')->setOption('required', false);
    $personForm->getValidator('identification_number')->setOption('required', false);
    $personForm->getValidator('identification_type')->setOption('required', false);
    $personForm->getValidator('sex')->setOption('required', false);
    $this->embedMergeForm('person', $personForm);
    $this->getWidget('occupation_id')->setLabel('Occupation');

    $this->setWidget('student_list', new csWidgetFormStudentMany(array('criteria' => new Criteria())));

    $this->getWidget('student_list')->setLabel('Students');

    $this->setValidator('student_list', new sfValidatorPass(array('required' => false)));
    $this->setValidator('person-address', new sfValidatorPass(array('required' => false)));

    $this->setWidget('nationality', new sfWidgetFormChoice(array('choices' => BaseCustomOptionsHolder::getInstance('Nationality')->getOptions())));
    $this->setValidator('nationality', new sfValidatorChoice(array('choices' => BaseCustomOptionsHolder::getInstance('Nationality')->getKeys(), 'required' => false)));

    $this->setDefault('student_list', array_map(create_function('$st', 'return $st->getStudentId();'), $this->getObject()->getStudentTutors()));
  }

  public function getFormFieldsDisplay()
  {
    return array(
      'Personal data' => array('person-lastname', 'person-firstname', 'person-identification_type', 'person-identification_number', 'is_alive', 'person-sex', 'person-cuil', 'person-birthdate', 'person-birth_country', 'person-birth_state', 'person-birth_city', 'tutor_type_id', 'person-observations'),
      'Statistics' => array('nationality', 'occupation_id', 'occupation_category_id', 'study_id'),
      'Contact data' => array('person-email', 'person-phone'),
      'In charge of' => array('student_list')
    );
  }

}