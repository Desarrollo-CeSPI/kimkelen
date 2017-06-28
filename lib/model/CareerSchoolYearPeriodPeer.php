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

class CareerSchoolYearPeriodPeer extends BaseCareerSchoolYearPeriodPeer
{

  static function getPeriodsSchoolYear($career_school_year_id)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    return self::retrieveOrdered($c);

  }

  static function getTrimesterPeriodsSchoolYear($career_school_year_id)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $c->add(self::COURSE_TYPE, CourseType::TRIMESTER);

    return self::retrieveOrdered($c);

  }

  static function getQuaterlyPeriodsSchoolYear($career_school_year_id)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $c->add(self::COURSE_TYPE, CourseType::QUATERLY);
    return self::retrieveOrdered($c);

  }

  static function getBimesterPeriodsSchoolYear($career_school_year_id)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $c->add(self::COURSE_TYPE, CourseType::BIMESTER);
    return self::retrieveOrdered($c);

  }

  static public function retrieveCurrents($course_type = null)
  {
    $c = self::retrieveCurrentsCriteria();
    $c->addAscendingOrderByColumn(CareerSchoolYearPeriodPeer::START_AT);
    if (!is_null($course_type))
    {
      $c->add(self::COURSE_TYPE, $course_type);
    }

    return self::retrieveOrdered($c);

  }

  static public function retrieveCurrentPeriodsIds($course_type = null)
  {
    $ids = array();
    foreach (self::retrieveOrdered($course_type) as $value)
    {
      $ids[]= $value->getId();
    }

    return $ids;
  }

  static public function retrieveCurrentsCriteria()
  {
    $c = new Criteria();
    $c->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());

    return $c;

  }

  static public function retrieveOrdered(Criteria $c = null)
  {
    $c = is_null($c) ? new Criteria() : $c;
    $c->addAscendingOrderByColumn(self::COURSE_TYPE);
    $c->addAscendingOrderByColumn(self::START_AT);

    return self::doSelect($c);

  }

  /**
   * This method retrieves current first Quaterly
   */
  static public function retrieveCurrentFirstQuaterly()
  {
    $c = self::retrieveCurrentsCriteria();
    $c->add(self::COURSE_TYPE, CourseType::QUATERLY);
    $c->addAscendingOrderByColumn(self::START_AT);

    return self::doSelectOne($c);

  }

  /**
   * This method retrieves current second Quaterly
   */
  static public function retrieveCurrentSecondQuaterly()
  {
    $c = self::retrieveCurrentsCriteria();
    $c->add(self::COURSE_TYPE, CourseType::QUATERLY);
    $c->addDescendingOrderByColumn(self::START_AT);

    return self::doSelectOne($c);

  }

  /**
   * This method retrieves current first Quaterly
   */
  static public function retrieveFirstQuaterlyForCareerSchoolYear($career_school_year)
  {
    $c = new Criteria();
    //$c = self::retrieveCriteriaForSchoolYear($school_year);
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $c->add(self::COURSE_TYPE, CourseType::QUATERLY);
    $c->addAscendingOrderByColumn(self::START_AT);

    return self::doSelectOne($c);

  }

  /**
   * This method retrieves current second Quaterly
   */
  static public function retrieveSecondQuaterlyForCareerSchoolYear($career_school_year)
  {
    $c = new Criteria();
    //$c = self::retrieveCriteriaForSchoolYear($school_year);
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $c->add(self::COURSE_TYPE, CourseType::QUATERLY);
    $c->addDescendingOrderByColumn(self::START_AT);

    return self::doSelectOne($c);

  }

  static public function retrieveCriteriaForSchoolYear($school_year)
  {
    $c = new Criteria();
    $c->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());

    return $c;

  }

  static public function retrieveByDay($day,$course_type)
  {
    $date = date('Y-m-d' ,$day);
    return self::retrieveByDateAndCourseType($date, $course_type);
  }

  static public function retrieveByDateAndCourseType($date, $course_type)
  {
    $c = new Criteria();
    $c->add(self::START_AT, $date, Criteria::LESS_THAN);
    $c->add(self::END_AT, $date, Criteria::GREATER_EQUAL);

    $c->add(self::COURSE_TYPE,$course_type);

    return self::doSelectOne($c);
  }


  static public function getPeriodsArrayForCourseType($course_type, $career_school_year_id)
  {
    $periods_array = array();
    if ($course_type == CourseType::TRIMESTER)
    {
      $periods = self::getTrimesterPeriodsSchoolYear($career_school_year_id);
    }
    elseif ($course_type == CourseType::QUATERLY)
    {
      $periods = self::getQuaterlyPeriodsSchoolYear($career_school_year_id);
    }
    else
    {
      $periods = self::getBimesterPeriodsSchoolYear($career_school_year_id);
    }

    foreach ($periods as $period)
    {
      $periods_array[$period->getShortName()] = $period;
    }

    return $periods_array;
  }
  
  static function retrieveByCourseTypeAndCareerSchoolYear($course_type,$career_school_year)
  {
      $c=new Criteria();
      $c->add(self::CAREER_SCHOOL_YEAR_ID,$career_school_year->getId());
      $c->add(self::COURSE_TYPE, $course_type);
      
      return self::doSelect($c);
      
  }

}