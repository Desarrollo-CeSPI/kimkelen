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

class StudentFreePeer extends BaseStudentFreePeer
{
  static public function retrieveCurrentCriteria(Criteria $criteria = null, $student_id)
  {
    if (is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->add(self::STUDENT_ID, $student_id);
    $criteria->addJoin(StudentFreePeer::CAREER_SCHOOL_YEAR_PERIOD_ID, CareerSchoolYearPeriodPeer::ID, Criteria::INNER_JOIN);
    $criteria->addJoin(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());

    return $criteria;
  }

  static public function retrieveCurrent(Criteria $criteria = null, $student_id)
  {
    return self::doSelect(self::retrieveCurrentCriteria($criteria, $student_id));
  }

  static public function retrieveCurrentAndIsFree(Criteria $criteria = null, $student_id)
  {
    $criteria = self::retrieveCurrentCriteria($criteria, $student_id);
    $criteria->add(self::IS_FREE, true);

    return self::doSelect($criteria);
  }

  /**
   * This method retrieves one Student free if exists, filtering by parameters
   *
   * @param Student $student
   * @param CareerSchoolYearPeriod $career_school_year_period
   * @param type $course_subject
   *
   * return StudentFree
   */
  static public function retrieveByStudentCareerSchoolYearCareerSchoolYearPeriodAndCourse(StudentCareerSchoolYear $student_career_school_year, CareerSchoolYearPeriod $career_school_year_period = null, $course_subject = null)
  {
    $c = new Criteria();
    $c->add(self::STUDENT_ID, $student_career_school_year->getStudentId());
    $c->add(self::IS_FREE, true);
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $student_career_school_year->getCareerSchoolYearId());

    if ( !is_null($career_school_year_period) && !is_null($career_school_year_period->getMaxAbsences()))
    { 
      $c->add(self::CAREER_SCHOOL_YEAR_PERIOD_ID, $career_school_year_period->getId());  
    }    

    if (!is_null($course_subject))
    {
      $c->add(self::COURSE_SUBJECT_ID, $course_subject->getId());
    }    

    return self::doSelectOne($c);
  }
}
