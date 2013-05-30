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
 * StudentReincorporation form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class StudentReincorporationForm extends BaseStudentReincorporationForm
{
  public function configure()
  {                
    $this->setWidget('student_id', new sfWidgetFormInputHidden());

    $this->getValidator('reincorporation_days')->setOption('min', 1);    
    
    $this->getWidget('career_school_year_period_id')->setOption('criteria', $this->getCareerSchoolYearPeriodFreeCriteria());
    $this->getWidget('career_school_year_period_id')->setLabel('Period');
    $this->getWidget('course_subject_id')->setOption('criteria', $this->getCourseSubjectCriteria());

    unset($this['created_at']);
  }

  private function getStudentId()
  {
    $user = sfContext::getInstance()->getUser();
    $student_id = $user->getReferenceFor('student');

    if (is_null($student_id))
    {
      $student_id = $user->getAttribute('student_id');
    }

    return $student_id;
  }
  
  public function getCareerSchoolYearPeriodFreeCriteria()
  {    
    $student_id = $this->getStudentId();
    
    $c = new Criteria();
    $c->add(StudentFreePeer::STUDENT_ID, $student_id);
    $c->add(StudentFreePeer::IS_FREE, true);
    $c->addJoin(StudentFreePeer::CAREER_SCHOOL_YEAR_PERIOD_ID, CareerSchoolYearPeriodPeer::ID, Criteria::INNER_JOIN);
    StudentFreePeer::retrieveCurrentCriteria($c, $student_id);
    
    return $c;
  }
  
  public function getCourseSubjectCriteria()
  {    
    $student_id = $this->getStudentId();
    
    $c = new Criteria();
    $c->add(StudentFreePeer::STUDENT_ID, $student_id);
    $c->add(StudentFreePeer::IS_FREE, true);
    $c->addJoin(StudentFreePeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID, Criteria::INNER_JOIN);
    StudentFreePeer::retrieveCurrentCriteria($c, $student_id);
    
    return $c;
  }

  public function doSave($con = null)
  {
    parent::doSave($con);    
    
    $student = StudentPeer::retrieveByPk($this->getValue('student_id'));

    $career_school_year_period = CareerSchoolYearPeriodPeer::retrieveByPk($this->getValue('career_school_year_period_id'));
    $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $career_school_year_period->getCareerSchoolYear());
    $course_subject = CourseSubjectPeer::retrieveByPk($this->getValue('course_subject_id'));
    $student_free = StudentFreePeer::retrieveByStudentCareerSchoolYearCareerSchoolYearPeriodAndCourse($student_career_school_year, $career_school_year_period, $course_subject);

    $student_free->setIsFree(false);
    $student_free->save($con);
  }
}