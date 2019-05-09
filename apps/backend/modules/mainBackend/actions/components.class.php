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
 * mainBackendComponents
 *
 * @author ncuesta
 */
class mainBackendComponents extends sfComponents
{
  protected function getShortcuts($peer_class, $criteria = null)
  {
    $criteria = (is_null($criteria))?new Criteria():$criteria;
    $criteria->addDescendingOrderByColumn(constant("$peer_class::ID"));
    $criteria->setLimit(sfConfig::get('app_home_shortcuts_count', 5));
    
    return call_user_func(array($peer_class, 'doSelect'), $criteria);
  }

  public function executeGeneralInformation()
  {
    /*is_deleted = false*/
	$criteria = new Criteria(SchoolYearPeer::DATABASE_NAME);
	$criteria->add(SchoolYearStudentPeer::IS_DELETED, false);
	
	$this->current_school_year = SchoolYearPeer::retrieveCurrent();
    $this->amount_sy_students = $this->current_school_year->countSchoolYearStudents($criteria);

    $this->amount_teachers = TeacherPeer::doCount(new Criteria());
    $this->amount_students = StudentPeer::doCount(new Criteria());
  }

  public function executeSchoolYearBox()
  {
    $this->shortcuts = $this->getShortcuts('SchoolYearPeer');
  }

  public function executeSubjectBox()
  {
    $this->shortcuts = $this->getShortcuts('SubjectPeer');
  }

  public function executeTeacherBox()
  {
    $this->shortcuts = $this->getShortcuts('TeacherPeer');
  }
  
  public function executeCareerBox()
  {
    $this->shortcuts = $this->getShortcuts('CareerPeer');
  }

  public function executeStudentBox()
  {
    $this->shortcuts = $this->getShortcuts('StudentPeer');
  }

  public function executeCourseBox()
  {
    $c = new Criteria();
    if ($this->getUser()->isTeacher())
    {
      $c->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
      $c->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID);
      $c->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
      $c->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
      $c->add(PersonPeer::USER_ID, $this->getUser()->getGuardUser()->getId());
    }
    elseif ($this->getUser()->isPreceptor())
    {
      #CoursePeer::getCoursesForPreceptorCriteria($this->getUser()->getGuardUser()->getId(), $c);
    }
    $c->add(CoursePeer::DIVISION_ID, null, Criteria::ISNULL);
    $this->shortcuts = $this->getShortcuts('CoursePeer',$c);
  }

  public function executeDivisionBox()
  {
    $c = new Criteria();
    if ($this->getUser()->isTeacher())
    {
      AdminGeneratorFiltersClass::addCourseTeacherCriteria($c, $this->getUser());
    }
    elseif ($this->getUser()->isPreceptor())
    {
      AdminGeneratorFiltersClass::addCoursePreceptorCriteria($c, $this->getUser());
    }
    $this->shortcuts = $this->getShortcuts('DivisionPeer',$c);
  }

}
