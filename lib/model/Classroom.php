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

class Classroom extends BaseClassroom
{

  public function __toString()
  {
    return $this->getName();

  }

  public function canBeDeleted()
  {
    $criteria = new Criteria();
    $criteria->add(CourseSubjectDayPeer::CLASSROOM_ID, $this->getId());
    return (CourseSubjectDayPeer::doCount($criteria) == 0);

  }

  /**
   * Returns an arrays of WeekDaysindicating each course that uses
   * this classroom
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
    foreach ($this->getCourseSubjectDaysJoinCourseSubject($criteria) as $course_day)
    {
      $days[] = $course_day->getWeekDay();
    }
    return $days;

  }

  public function getResources()
  {
    return ResourcesPeer::getallByClassRoom($this->getId());
  }

  public function getResourcesStr(){
    $str = '';
    foreach ($this->getResources() as $resource){
      $str .= $resource .' ';
    }
    return $str;
  }

}