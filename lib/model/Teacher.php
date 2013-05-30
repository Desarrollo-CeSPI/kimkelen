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

class Teacher extends BaseTeacher
{

  /**
   * This method implements __toString method to print the object
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getPersonFullName();

  }

  /**
   * Proxies getPerson()->$method as getPersonMethod in current object. Only for getters
   *
   * @param string $method
   * @param <type> $arguments
   * @return <type>
   */
  public function __call($method, $arguments)
  {
    if (preg_match('/^getPerson(.*)/', $method, $matches) && isset($matches[1]))
    {
      $method = "get" . $matches[1];
      return $this->getPerson()->$method();
    }
    if (preg_match('/^canPersonBe(.*)/', $method, $matches) && isset($matches[1]))
    {
      $method = "canBe" . $matches[1];
      return $this->getPerson()->$method();
    }
    parent::__call($method, $arguments);

  }

  /**
   * Returns if this person can be set to active. This will be only when is not active
   * @return boolean
   */
  public function canBeActivated()
  {
    return $this->getIsActive() == false;

  }

  public function haveCourses()
  {
    return $this->countCourseSubjectTeachers();

  }

  public function getMessageCantHaveCourses()
  {
    return 'Dont have any course assigned';

  }

  public function getCountHours()
  {
    $hours = array(0 => '00', 1 => '00', 2 => '00');
    foreach ($this->getCourseSubjectTeachers() as $course_subject_teacher)
    {
      $course_hours = $course_subject_teacher->getCourseSubject()->getCountHours();
      $hours[0] += $course_hours[0];
      $hours[1] += $course_hours[1];
      $hours[2] += $course_hours[2];
    }
    $hours = implode(':', $hours);
    return $hours;

  }

  /**
   * Returns an arrays of WeekDaysindicating each course that this teacher
   * dictates
   *
   * @return array
   */
  public function getWeekCalendar(SchoolYear $school_year = null)
  {
    if (is_null($school_year))
      $school_year = SchoolYearPeer::retrieveCurrent();
    $career_subject_school_years = array();

    foreach ($school_year->getCareerSchoolYearsJoinCareer() as $career_school_year)
    {
      foreach ($career_school_year->getCareerSubjectSchoolYears() as $css)
      {
        $career_subject_school_years[] = $css->getId();
      }
    }

    $criteria = new Criteria();
    $criteria->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $career_subject_school_years, Criteria::IN);

    $days = array();

    foreach ($this->getCourseSubjectTeachersJoinCourseSubject($criteria) as $course_subject_teacher)
    {
      foreach ($course_subject_teacher->getCourseSubject()->getCourseSubjectDays() as $course_day)
      {
        $days[] = $course_day->getWeekDay();
      }
    }

    return $days;

  }

  public function createPreceptor(PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $personal = new Personal();
    $personal->setPerson($this->getPerson());
    $personal->save($con);

    $guard_user = $this->getPersonSfGuardUser();
    if (!is_null($guard_user))
    {
      $personal_group = BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::PERSONAL);
      if (!array_key_exists($personal_group, $guard_user->getGroups()))
      {
        $guard_user->addGroupByName($personal_group);
        $guard_user->save($con);
      }
    }

  }

  public function canAddPreceptor()
  {
    $c = new Criteria();
    $c->add(PersonalPeer::PERSON_ID, $this->getPersonId());

    return PersonalPeer::doCount($c) == 0;

  }

  public function getMessageCantAddPreceptor()
  {
    return 'The teacher is already a preceptor.';

  }

  public function getSubjects()
  {
    $course_subject_teachers = $this->getCourseSubjectTeachers();
    $subjects = array();
    foreach ($course_subject_teachers as $course_subject_teacher)
    {
      if (!in_array($course_subject_teacher->getSubject(), $subjects))
      {
        $subjects[] = $course_subject_teacher->getSubject();
      }
    }

    return $subjects;

  }

  public function getTelefon()
  {
    return $this->getPerson()->getPhone();
  }

  public function getEmail()
  {
    return $this->getPerson()->getEmail();
  }

}

sfPropelBehavior::add('Teacher', array('person_delete'));