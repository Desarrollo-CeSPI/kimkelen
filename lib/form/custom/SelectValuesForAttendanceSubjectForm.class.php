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

class SelectValuesForAttendanceSubjectForm extends sfForm
{

  public function configure()
  {
    parent::configure();

    $this->widgetSchema->setNameFormat('multiple_student_attendance[%s]');
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $this->setWidget('career_school_year_id', new sfWidgetFormPropelChoice(array('model' => 'CareerSchoolYear', 'add_empty' => true, 'criteria' => $c)));
    $this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear','required'=>true)));

    #widget de año lectivo
    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'multiple_student_attendance_career_school_year_id',
        "message_with_no_value" => "Seleccione una carrera y apareceran los años que correspondan",
        'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));

    $this->setValidator('year', new sfValidatorString(array('required'=>true)));
    #widget Course subjecct
      $course_subject_widget = new sfWidgetFormPropelChoice(array('model' => 'CourseSubject', 'add_empty' => true, 'method' => "FullToString",));

    $this->setWidget('course_subject_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $course_subject_widget,
        'observe_widget_id' => 'multiple_student_attendance_year',
        "message_with_no_value" => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getCourseSubjects')
      )));

    $this->setValidator('course_subject_id', new sfValidatorPropelChoice(array('required' => true, 'model' => 'CourseSubject')));
    #widget Fecha
    $this->setWidget('day', new csWidgetFormDateInput());
    $this->setValidator('day', new mtValidatorDateString(array('required' => true,'date_output'=>'Y-m-d')));
  }

  public static function getCourseSubjects($widget, $values)
  {
    $sf_user = sfContext::getInstance()->getUser();
    $career_school_year = CareerSchoolYearPeer::retrieveByPK(sfContext::getInstance()->getUser()->getAttribute('career_school_year_id'));
    $career = $career_school_year->getCareer();


    $c = new Criteria();

    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $c->addJoin(CourseSubjectPeer::COURSE_ID,  CoursePeer::ID);
    $c->add(CoursePeer::SCHOOL_YEAR_ID,  SchoolYearPeer::retrieveCurrent()->getId());
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(CareerSubjectPeer::YEAR, $values);



    if ($sf_user->isPreceptor())
    {
      $course_ids = PersonalPeer::retrieveCourseIdsjoinWithDivisionCourseOrCommission($sf_user->getGuardUser()->getId(), true);
      $c->add(CoursePeer::ID, $course_ids, Criteria::IN);

      $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    }

    $course_ids = array();
    foreach(CourseSubjectPeer::doSelect($c) as $course_subject)
    {
      if ($course_subject->hasAttendanceForSubject())
        $course_ids[] = $course_subject->getId();
    }

    $criteria = new Criteria();
    $criteria->add(CourseSubjectPeer::ID, $course_ids, Criteria::IN);
    $criteria->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    CareerSubjectSchoolYearPeer::sorted($criteria);

    $widget->setOption('criteria', $criteria);
  }

  public static function getYears($widget, $values)
  {
    $career = CareerSchoolYearPeer::retrieveByPk($values)->getCareer();
    $choices = $career->getYearsForOption(true);
    $widget->setOption('choices', $choices);
    sfContext::getInstance()->getUser()->setAttribute('career_school_year_id', $values);
  }


}