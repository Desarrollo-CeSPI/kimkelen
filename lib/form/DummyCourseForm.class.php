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

class DummyCourseForm extends CourseForm
{
  public function configure()
  {
    parent::configure();
    //Widgets
    $this->setWidget('school_year_id', new sfWidgetFormReadOnly(array('plain' => false,'value_callback' => array('SchoolYearPeer', 'retrieveByPk'))));
    $school_year = SchoolYearPeer::retrieveCurrent();
    $this->setDefault('school_year_id', $school_year->getId());

    $criteria_career = new Criteria();
    $criteria_career->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $this->setWidget('career_school_year_id' , new sfWidgetFormPropelChoice(array('model' => 'CareerSchoolYear','criteria' => $criteria_career, 'add_empty' => true)));
    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->widgetSchema['year'] = new dcWidgetAjaxDependence(array(
      'dependant_widget'            => $w,
      'observe_widget_id'           => 'course_career_school_year_id',
      "message_with_no_value"       => "Seleccione una carrera y apareceran los años que correspondan",
      'get_observed_value_callback' => array(get_class($this), 'getYears')
    ));

    //Check if all the subjects has the same CareerSchoolYear
    $career_school_year = $this->getObject()->getCareerSchoolYear();
    $career_school_year_id = (is_null($career_school_year))?null:$career_school_year->getId();
    $this->getWidget('career_school_year_id')->setDefault($career_school_year_id);

    //Check if all the subjects has the same Year
    $year = $this->getObject()->getYear();
    $this->getWidget('year')->setDefault($year);

    $widget = new sfWidgetFormPropelChoice(array('model' => 'CareerSubjectSchoolYear', 'multiple' => true));

    $this->widgetSchema['course_subjects']= new dcWidgetAjaxDependence(array(
        'dependant_widget'            => $widget,
        'observe_widget_id'           => 'course_year',
        "message_with_no_value"       => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getSubjects')
    ));
    
    //Validators
    $this->setValidator('career_school_year_id' , new  sfValidatorPropelChoice(array('model' => 'CareerSchoolYear', 'criteria' => $criteria_career)));
    $this->setValidator('course_subjects' , new  sfValidatorPropelChoice(array('model' => 'CareerSubjectSchoolYear' ,'required' => true, 'multiple' => true)));
    $this->setValidator('school_year_id', new sfValidatorPass());
    $this->setValidator('year', new sfValidatorNumber());
  }

  public static function getYears($widget, $values)
  {
    $career = CareerSchoolYearPeer::retrieveByPk($values)->getCareer();
    $choices = $career->getYearsForOption(true);
    $widget->setOption('choices' , $choices);
    sfContext::getInstance()->getUser()->setAttribute('career_id', $career->getId());
  }
  
  public static function getSubjects($widget, $values)
  {
    $career_id = sfContext::getInstance()->getUser()->getAttribute('career_id');
    $criteria = new Criteria();
    $criteria->add(CareerSubjectPeer::CAREER_ID, $career_id);
    $criteria->add(CareerSubjectPeer::YEAR,$values);
    $criteria->add(CareerSubjectPeer::IS_OPTION,false);
    $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID,SubjectPeer::ID);
    $criteria->addJoin(CareerSubjectPeer::ID,CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    $criteria->addAscendingOrderByColumn(SubjectPeer::NAME);
    $widget->setOption('criteria' , $criteria);
  }

  public function doSave($con = null)
  {
    parent::doSave($con);
    $this->doSaveCourseSubjects($con);
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    $values = array();
    foreach ($this->getObject()->getCourseSubjects() as $course_subject)
    {
      $values[] = $course_subject->getCareerSubjectSchoolYearId();
    }
    $this->setDefault('course_subjects', $values);
  }

  public function doSaveCourseSubjects($con)
  {
    if (array_key_exists('course_subjects', $this->values))
    {
      foreach ($this->getObject()->getCourseSubjects() as $course_subject)
      {
        $course_subject->delete($con);
      }
      
      foreach($this->values['course_subjects'] as $career_subject_school_year_id)
      {
        $course_subject = new CourseSubject();
        $course_subject->setCourse($this->getObject());
        $course_subject->setCareerSubjectSchoolYearId($career_subject_school_year_id);
        
        $course_subject->save($con);
      }
    }
  }
}