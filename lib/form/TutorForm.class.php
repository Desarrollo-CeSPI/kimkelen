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
 * Tutor form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class TutorForm extends BaseTutorForm
{

  public function configure() {
    unset($this['person_id']);
    $person = $this->getObject()->getPerson();
    if (is_null($person)) {
        $person = new Person();
        $this->getObject()->setPerson($person);
    }
    $personForm = new PersonForm($person, array('related_class'=>'tutor','embed_as'=>'person'));
    $personForm->getValidator('phone')->setOption('required', false);
    $this->embedMergeForm('person',$personForm);
    $this->getWidget('occupation_id')->setLabel('Occupation');

    $this->setWidget('student_list',
            new csWidgetFormStudentMany(array('criteria'=> new Criteria())));

    $this->getWidget('student_list')->setLabel('Students');

    $this->setValidator('student_list', new sfValidatorPass());

    $this->setWidget('nationality', new sfWidgetFormChoice(array('choices' => BaseCustomOptionsHolder::getInstance('Nationality')->getOptions())));
    $this->setValidator('nationality', new sfValidatorChoice(array('choices' => BaseCustomOptionsHolder::getInstance('Nationality')->getKeys(), 'required' => false)));

    $this->setDefault('student_list',
            array_map(create_function('$st', 'return $st->getStudentId();'),
              $this->getObject()->getStudentTutors()));
  }

  public function getFormFieldsDisplay()
  {
    return array(
          'Personal data'   =>  array( 'person-lastname', 'person-firstname', 'person-identification_type', 'is_alive', 'person-identification_number', 'person-sex', 'person-cuil', 'person-birthdate', 'person-birth_country', 'person-birth_state','person-birth_department' ,'person-birth_city', 'tutor_type_id', 'person-observations' ),
          'Statistics'      => array('nationality' ,'occupation_id', 'occupation_category_id', 'study_id'),
          'Contact data'   => array('person-email', 'person-phone', 'person-address'),
		  'System access'  => array('person-username', 'person-password', 'person-password_again'),
          'In charge of'  => array('student_list')
    );
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);
    $this->saveStudentList($con);
  }

  public function saveStudentList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }
    $st_list_w = $this->getWidget('student_list');
    if (!isset( $st_list_w ))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $con->beginTransaction();
    try
    {
      $this->getObject()->deleteStudents($con);
      $values = $this->getValue('student_list');
      if (is_array($values))
      {
          foreach ($values as $value)
          {
            $student_tutor = new StudentTutor();
            $student_tutor->setTutor($this->getObject());
            $student_tutor->setStudentId($value);
            $student_tutor->save($con);
          }
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
    }

  }


}
