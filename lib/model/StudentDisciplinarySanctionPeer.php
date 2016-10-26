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

class StudentDisciplinarySanctionPeer extends BaseStudentDisciplinarySanctionPeer
{
  /**
   * Retrieves rows from table student_advice that matches with the student and school year given
   * and that were registered between starting and ending day of the period given as parameter, and then
   * sumarizes value field from all retrieved rows.
   *
   * @param Student $student
   * @param SchoolYear $school_year
   * @param CareerSchoolYearPeriod $period
   * @return integer
   */
  public static function countStudentDisciplinarySanctionsForPeriod(Student $student, SchoolYear $school_year = null, CareerSchoolYearPeriod  $period = null)
  {
    $c = new Criteria();
    if (is_null($school_year))
    {
      $school_year = SchoolYearPeer::retrieveCurrent();
    }
    $c->add(self::SCHOOL_YEAR_ID, $school_year->getId());
    $c->add(self::STUDENT_ID, $student->getId());

    //Check for sanctions type considered in report_card
    $c->addJoin(self::SANCTION_TYPE_ID, SanctionTypePeer::ID);
    $c->add(SanctionTypePeer::CONSIDERED_IN_REPORT_CARD, true);

    if (!is_null($period))
    {
      $c->add(self::REQUEST_DATE, $period->getStartAt(), Criteria::GREATER_EQUAL);
      $c->addAnd(self::REQUEST_DATE, $period->getEndAt(), Criteria::LESS_EQUAL);
    }

    $sanctions_array = self::doSelect($c);
    $total = 0;
    foreach ($sanctions_array as $sanction)
    {
      $total+= $sanction->getValue();
    }

    return $total;
  }

  public static function countStudentDisciplinarySanctions($student, $school_year, $periods)
  {
    $total = 0;
    foreach ($periods as $period)
    {
      $total += self::countStudentDisciplinarySanctionsForPeriod($student, $school_year, $period);
    }

    return $total;
  }

  public static function countStudentDisciplinarySanctionsForSchoolYear($school_year)
  {
    $criteria = new Criteria();
    $criteria->add(self::SCHOOL_YEAR_ID, $school_year->getId());
    $criteria->clearSelectColumns();
    $criteria->addSelectColumn(self::STUDENT_ID);
    $criteria->setDistinct();

    return self::doCount($criteria);
  }

  /**
   *
   *
   * @param Student $student
   * @param SchoolYear $school_year
   * @param CareerSchoolYearPeriod $period
   * @return integer
   */
  public static function retrieveStudentDisciplinarySanctionsForPeriod(Student $student, SchoolYear $school_year = null, CareerSchoolYearPeriod  $period = null)
  {
    $c = new Criteria();
    if (is_null($school_year))
    {
      $school_year = SchoolYearPeer::retrieveCurrent();
    }
    $c->add(self::SCHOOL_YEAR_ID, $school_year->getId());
    $c->add(self::STUDENT_ID, $student->getId());

    //Check for sanctions type considered in report_card
    $c->addJoin(self::SANCTION_TYPE_ID, SanctionTypePeer::ID);
    $c->add(SanctionTypePeer::CONSIDERED_IN_REPORT_CARD, true);

    if (!is_null($period))
    {
      $c->add(self::REQUEST_DATE, $period->getStartAt(), Criteria::GREATER_EQUAL);
      $c->addAnd(self::REQUEST_DATE, $period->getEndAt(), Criteria::LESS_EQUAL);
      $c->addAscendingOrderByColumn(self::REQUEST_DATE);
    }

    return self::doSelect($c);
  }

    public static function countTotalValueForStudent(Student $student)
  {
    $c = new Criteria();
    $c->add(self::STUDENT_ID, $student->getId());

    //$c->clearSelectColumns();
    //$c->addSelectColumn('SUM(' . self::VALUE. ')');

    $total = 0;
    foreach (self::doSelect($c) as $sds)
    {
      $total+= $sds->getValue();
    }

    return $total;
  }
  
  public static function retrieveStudentDisciplinarySanctionsForSchoolYear($student)
  { 
	$school_year = SchoolYearPeer::retrieveCurrent()->getYear();
    $criteria = new Criteria();
    $criteria->add(self::REQUEST_DATE, 'YEAR('.self::REQUEST_DATE.')='. $school_year, Criteria::CUSTOM);
    $criteria->clearSelectColumns();
    $criteria->add(self::STUDENT_ID, $student->getId());
    $criteria->setDistinct();
    return self::doSelect($criteria);
  }
  
  public static function countStudentDisciplinarySanctionsForSchoolYearAndStudent($student)
  {
	$school_year = SchoolYearPeer::retrieveCurrent()->getYear();
    $criteria = new Criteria();
    $criteria->add(self::REQUEST_DATE, 'YEAR('.self::REQUEST_DATE.')='. $school_year, Criteria::CUSTOM);
    $criteria->clearSelectColumns();
    $criteria->add(self::STUDENT_ID, $student->getId());
    $criteria->setDistinct();

    return self::doCount($criteria);
  }
}
