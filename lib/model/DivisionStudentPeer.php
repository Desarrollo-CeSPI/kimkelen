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

class DivisionStudentPeer extends BaseDivisionStudentPeer
{
  public static function doCountForCareerSchoolYearShiftAndDivision($career_school_year, $shift, $division)
  {
    $criteria = new Criteria();
    $criteria->addJoin(self::DIVISION_ID, DivisionPeer::ID);
    $criteria->add(DivisionPeer::ID, $division->getId());
    $criteria->addJoin(self::STUDENT_ID, StudentPeer::ID);
    $criteria->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $criteria->add(DivisionPeer::SHIFT_ID, $shift->getId());

    return StudentPeer::doCount($criteria);
  }

  public static function doCountStudentsForCareerSchoolYearShiftAndYear($career_school_year, $shift, $year, $criteria = null)
  {
    $criteria = is_null($criteria) ? new Criteria() : $criteria;
    $criteria->addJoin(self::DIVISION_ID, DivisionPeer::ID);
    $criteria->add(DivisionPeer::SHIFT_ID, $shift->getId());

    return self::doCountStudentsForCareerSchoolYearAndYear($career_school_year, $year, $criteria);
  }

  public static function doCountStudentsForCareerSchoolYearAndYear($career_school_year, $year, $criteria = null)
  {
    $criteria = is_null($criteria) ? new Criteria() : $criteria;
    $criteria->addJoin(self::STUDENT_ID, StudentPeer::ID);
    $criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
    $criteria->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    //$criteria->add(StudentCareerSchoolYearPeer::YEAR, $year);
    $criteria->add(DivisionPeer::YEAR, $year);
    $criteria->setDistinct();

    return StudentPeer::doCount($criteria);
  }

  public static function doCountForCareerSchoolYearShiftYearAndDivision($career_school_year, $shift, $year, $division)
  {
    $criteria = new Criteria();
    $criteria->add(self::DIVISION_ID, $division->getId());

    return self::doCountStudentsForCareerSchoolYearShiftAndYear($career_school_year, $shift, $year, $criteria);
  }

  public static function doSelectForCareerSchoolYearShiftAndYear($career_school_year, $shift, $year)
  {
    $criteria = new Criteria();
    $criteria->add(DivisionPeer::YEAR, $year);

    return self::doSelectDivisionsForCareerSchoolYearAndShift($career_school_year, $shift, $criteria);
  }

  public static function doSelectDivisionsForCareerSchoolYearAndShift($career_school_year, $shift, $criteria = null)
  {
    $criteria = is_null($criteria) ? new Criteria() : $criteria;
    $criteria->addJoin(self::DIVISION_ID, DivisionPeer::ID);
    $criteria->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $criteria->addAnd(DivisionPeer::SHIFT_ID, $shift->getId());
    $criteria->setDistinct();

    return DivisionPeer::doSelect($criteria);
  }

  public static function retrieveDivisionsForStudentAndYear($student, $year)
  {
    $c = new Criteria();
    $c->addJoin(self::DIVISION_ID, DivisionPeer::ID, Criteria::INNER_JOIN);
    $c->add(self::STUDENT_ID, $student->getId());
    $c->add(DivisionPeer::YEAR, $year);

    return DivisionPeer::doSelect($c);
  }

}