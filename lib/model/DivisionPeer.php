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

class DivisionPeer extends BaseDivisionPeer
{

  /**
   * This method returns all the divisions of the SchoolYear passed, if is null the school_year then returns the dvisiosons of the current school_year.
   *
   * @param Criteria $criteria
   * @param SchoolYear $school_year
   * @return <array> Division
   */
  public static function retrieveSchoolYearDivisions(Criteria $criteria = null, SchoolYear $school_year = null)
  {
    $school_year = is_null($school_year) ? SchoolYearPeer::retrieveCurrent() : $school_year;

    $criteria = is_null($criteria) ? new Criteria() : $criteria;
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $criteria->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $criteria->addAscendingOrderByColumn(self::YEAR);
    $criteria->addAscendingOrderByColumn(self::DIVISION_TITLE_ID);

    return self::doSelect($criteria);
  }

  public static function search($query, $sf_user)
  {
    $c = new Criteria();
    $c->addJoin(self::DIVISION_TITLE_ID, DivisionTitlePeer::ID);
    $criterion = $c->getNewCriterion(DivisionTitlePeer::NAME, $query, Criteria::LIKE);

    $criterion->addOr($c->getNewCriterion(self::YEAR, $query));

    $c->add($criterion);

    if ($sf_user->isPreceptor())
    {
      PersonalPeer::joinWithDivisions($c, $sf_user->getGuardUser()->getId());
    }
    elseif ($sf_user->isTeacher())
    {
      TeacherPeer::joinWithDivisions($c, $sf_user->getGuardUser()->getId());
    }


    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $c->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);

    return self::doSelect($c);

  }

  public static function retrieveByDivisionTitleAndYearAndSchoolYear(DivisionTitle $division_title, $year, $career_school_year)
  {
    $c = new Criteria();
    $c->add(self::DIVISION_TITLE_ID, $division_title->getId());
    $c->add(self::YEAR, $year);
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());

    return self::doSelectOne($c);
  }

  /**
   * This method return true if there is not a division with $division_title_id, $career_school_year_id and $year
   */
  public static function checkUnique($division_title_id, $career_school_year_id, $year)
  {
    $c = new Criteria();
    $c->add(self::DIVISION_TITLE_ID, $division_title_id);
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $c->add(self::YEAR, $year);

    return is_null(self::doSelectOne($c));

  }

  public static function retrieveByStudentCareerSchoolYear(StudentCareerSchoolYear $student_career_school_year)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $student_career_school_year->getCareerSchoolYearId());
    $c->addJoin(DivisionStudentPeer::DIVISION_ID, self::ID);
    $c->add(DivisionStudentPeer::STUDENT_ID, $student_career_school_year->getStudentId());

    return self::doSelectOne($c);
  }

  public static function sorted(Criteria $c)
  {
    $c->addAscendingOrderByColumn(self::YEAR);
    $c->addJoin(self::DIVISION_TITLE_ID, DivisionTitlePeer::ID);
    $c->addAscendingOrderByColumn(DivisionTitlePeer::NAME);
  }

    /**
   * This method returns all the divisions of the SchoolYear passed, if is null the school_year then returns the dvisiosons of the current school_year.
   *
   * @param Criteria $criteria
   * @param SchoolYear $school_year
   * @return <array> Division
   */
  public static function retrieveCareerSchoolYearDivisions(Criteria $criteria = null, $career_school_year)
  {
    $criteria = is_null($criteria) ? new Criteria() : $criteria;
    $criteria->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $criteria->addAscendingOrderByColumn(self::YEAR);

    return self::doSelect($criteria);
  }

    /**
   * This method returns all the divisions of the SchoolYear passed fot the student given
   *
   * @param Student $student
   * @param SchoolYear $school_year
   * @return <array> Division
   */
  public static function retrieveStudentSchoolYearDivisions($school_year, $student)
  {
    $criteria = new Criteria();
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $criteria->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $criteria->addJoin(self::ID, DivisionStudentPeer::DIVISION_ID);
    $criteria->add(DivisionStudentPeer::STUDENT_ID, $student->getId());
    $criteria->addAscendingOrderByColumn(self::YEAR);
    $criteria->addAscendingOrderByColumn(DivisionPeer::DIVISION_TITLE_ID);
    return self::doSelect($criteria);

  }

  public static function retrieveCareerSchoolYearAndYearDivisions($career_school_year, $year)
  {
    $criteria = new Criteria();
    $criteria->add(self::YEAR, $year);

    return self::retrieveCareerSchoolYearDivisions($criteria, $career_school_year);
  }

  static public function retrieveNextDivisionFor($division, $sf_user)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $division->getCareerSchoolYearId());
    self::sortedJoinWithPreceptor($c, $sf_user);

    $divisions = self::doSelect($c);

    $i = 0;
    while ($divisions[$i]->getId() != $division->getId())
    {
      $i++;
    }

    if ($i == count($divisions)-1)
    {
      return $divisions[0];      
    }

    return $divisions[$i + 1];
  }

  static public function retrievePreviousDivisionFor($division, $sf_user)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $division->getCareerSchoolYearId());
    self::sortedJoinWithPreceptor($c, $sf_user);

    $divisions = self::doSelect($c);

    $i = 0;
    while ($divisions[$i]->getId() != $division->getId())
    {
      $i++;
    }
    
    if ($i == 0)
    {
      //return $divisions[count($divisions)-1];      
      return end($divisions);
    }

    return $divisions[$i - 1];
  }

  static public function sortedJoinWithPreceptor($c, $sf_user)
  {
    self::sorted($c);
    if ($sf_user->isPreceptor())
    {
      PersonalPeer::joinWithDivisions($c, $sf_user->getGuardUser()->getId());
    }

    return $c;
  }


}
