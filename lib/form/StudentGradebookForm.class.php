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
class StudentGradebookForm extends sfForm
{

  public function configure()
  {
    parent::configure();

    $this->object = $this->getOption('student');
    $criteria= new Criteria();
    $criteria->addJoin(CareerPeer::ID, CareerStudentPeer::CAREER_ID );
    $criteria->add(CareerStudentPeer::STUDENT_ID, $this->object->getId());
    $career_criteria = $criteria;
    $this->setWidget('career_id',new sfWidgetFormPropelChoice(array(
      'model' => 'Career', 
      'criteria' => $criteria,
      'add_empty' => false)));

    $criteria= new Criteria();
    $criteria->addJoin(SchoolYearPeer::ID, StudentSchoolYearPeer::SCHOOL_YEAR_ID );
    $criteria->add(StudentSchoolYearPeer::STUDENT_ID, $this->object->getId());
    $school_year_criteria = $criteria;
    $this->setWidget('school_year_id', new sfWidgetFormPropelChoice(array(
      'model' => 'SchoolYear',
      'criteria' => $criteria,
    )));

    $year = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
      'dependant_widget'            => $year,
      'observe_widget_id'           => 'gradebook_career_id',
      "message_with_no_value"       => "Seleccione una carrera y apareceran los años que correspondan",
      'get_observed_value_callback' => array($this, 'getYears')
    )));

    $this->setWidget('student_id', new sfWidgetFormInputHidden());
    $this->setDefault('student_id',$this->object->getId());

    $this->setValidators(array(
      'student_id'        => new sfValidatorPropelChoice(array('required' =>true, 'model' => 'Student', 'column' => 'id')),
      'career_id'         => new sfValidatorPropelChoice(array('required' =>true, 'model' => 'Career', 'column' => 'id', 'criteria' => $career_criteria)),
      'school_year_id'    => new sfValidatorPropelChoice(array('required' =>true, 'model' => 'DivisionTitle', 'column' => 'id', 'criteria' => $school_year_criteria)),
      'year'              => new sfValidatorInteger(array( 'required' =>false,) ),
    ));


    $this->widgetSchema->setLabels(array(
      'career_id' => 'Carrera',
      'school_year_id' => 'Año lectivo',
      'year' => 'Año'
    ));

    $this->getWidgetSchema()->setNameFormat('gradebook[%s]');
  }


  public static function getYears($widget, $values)
  {
    $choices = CareerPeer::retrieveByPk($values)->getYearsForOption(true);
    $widget->setOption('choices' , $choices);
  }

}