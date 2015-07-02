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

class CareerSchoolYearPeer extends BaseCareerSchoolYearPeer
{
  /**
   * Returns a career school year by $career and $school_year.
   *
   * @param Career $career
   * @param SchoolYear $school_year
   * @return CareerSchoolYear
   */
  public static function retrieveByCareerAndSchoolYear($career, $school_year)
  {
    if (is_null($career)  | is_null($school_year))
      return null;

    $c = new Criteria();
    $c->add(self::CAREER_ID, $career->getId());
    $c->add(self::SCHOOL_YEAR_ID, $school_year->getId());

    return self::doSelectOne($c);
  }

  /**
   * If $school_year is null, then is the current school_year by default
   *
   * @param Criteria $criteria
   * @param type $school_year
   *
   * @return array[] CareerSchoolYear
   */
  public static function retrieveBySchoolYear(Criteria $criteria = null , $school_year = null)
  {
    $criteria = is_null($criteria) ? new Criteria() : $criteria;
    $school_year = is_null($school_year) ? SchoolYearPeer::retrieveCurrent() : $school_year;

    $criteria->add(self::SCHOOL_YEAR_ID, $school_year->getId());

    return self::doSelect($criteria);
  }


  public static function retrieveCurrentForStudentCriteria(Student $student, Criteria $c = null)
  {
    $c = is_null($c) ? new Criteria() : $c;

    $c->add(self::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $c->addJoin(self::ID, StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID);
    $c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $student->getId());

    return $c;
  }

  public static function sort(Criteria $criteria = null)
  {
    $criteria->addJoin(self::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
    $criteria->addDescendingOrderByColumn(SchoolYearPeer::YEAR);

    return self::doSelect($criteria);
  }
}