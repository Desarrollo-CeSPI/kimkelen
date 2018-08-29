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

class StudentCareerSchoolYearPeer extends BaseStudentCareerSchoolYearPeer
{
  static public function countByCareerAndStudent($career_id, $student_id, $school_year_id , PropelPDO $con = null)
  {
    $c = new Criteria();

    $c->add(self::STUDENT_ID, $student_id);
    $c->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(CareerSchoolYearPeer::CAREER_ID, $career_id);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year_id);

    return self::doCount($c, $con);
  }

  /**
   * Returns the current student_career_school_year for the given student and career_school_year.
   *
   * @param Student $student
   * @param CareerSchoolYear $career_school_year
   * @return StudentCareerSchoolYear
   */
  public static function getCurrentForStudentAndCareerSchoolYear(Student $student, CareerSchoolYear $career_school_year)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $c->add(self::STUDENT_ID, $student->getId());
    $c->addDescendingOrderByColumn(self::YEAR);

    return self::doSelectOne($c);
  }

  /**
   * Returns the last student_career_school_year approved for the given student and career_school_year.
   *
   * @todo refactor: remove the $career_school_year parameter, it's not used anymore.
   * @param Student $student
   * @param CareerSchoolYear $career_school_year
   *
   * @return StudentCareerSchoolYear
   */
  public static function getLastStudentCareerSchoolYearApproved(Student $student, CareerSchoolYear $career_school_year)
  {
    $c = new Criteria();
    $c->add(self::STUDENT_ID, $student->getId());
    $c->add(self::STATUS, StudentCareerSchoolYearStatus::APPROVED);
    $c->addDescendingOrderByColumn(self::YEAR);

    return self::doSelectOne($c);
  }

  public static function retrieveCareerSchoolYearForStudentAndYear($student, $school_year)
  {
    $c = new Criteria();
    $c->add(self::STUDENT_ID, $student->getId());
    $c->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());

    $student_career_school_years = self::doSelect($c);

    self::clearInstancePool();
    return $student_career_school_years;
  }

  public static function retrieveLastYearApprovedStudentCriteria($career_school_year)
  {
    $c = self::retrieveLastyearStudentCriteria($career_school_year);
    $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::APPROVED);

    return $c;
  }

  public static function retrieveLastYearRepprovedStudentCriteria($career_school_year)
  {
    $c = self::retrieveLastyearStudentCriteria($career_school_year);
    $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::REPPROVED);

    return $c;
  }

  public static function retrieveLastYearStudentNotGraduatedCriteria($career_school_year)
  {
    $school_year = SchoolYearPeer::retrieveLastYearSchoolYear($career_school_year->getSchoolYear());
    $c = new Criteria();
    $c->addJoin(StudentPeer::ID, CareerStudentPeer::STUDENT_ID, Criteria::INNER_JOIN);
    $c->addAnd(CareerStudentPeer::STATUS, CareerStudentStatus::GRADUATE, criteria::NOT_EQUAL);
    $c->addAnd(CareerStudentPeer::CAREER_ID, $career_school_year->getCareer()->getId());
    $c->addAnd(StudentPeer::ID, SchoolYearStudentPeer::retrieveStudentIdsForSchoolYear($school_year), Criteria::IN);
    return $c;
  }

    public static function doCountForCareerSchoolYearAndYear($career_school_year, $year, $criteria = null)
  {
    $criteria = is_null($criteria) ? new Criteria() : $criteria;
    $criteria->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    //$criteria->addAnd(self::YEAR, $year);
    $criteria->addJoin(DivisionStudentPeer::STUDENT_ID, self::STUDENT_ID);
    $criteria->addJoin(DivisionPeer::ID, DivisionStudentPeer::DIVISION_ID);
    $criteria->addAnd(DivisionPeer::YEAR, $year);
    $criteria->addJoin(SchoolYearStudentPeer::STUDENT_ID, self::STUDENT_ID);
    $criteria->addJoin(SchoolYearStudentPeer::SCHOOL_YEAR_ID, $career_school_year->getSchoolYearId());
    $criteria->add(SchoolYearStudentPeer::IS_DELETED,false);
    $criteria->setDistinct();

    return self::doCount($criteria);
  }


  public static function countStudentsNotInAnyDivisionForCareerSchoolYear($career_school_year, PropelPDO $con = null)
  {
    $c = new Criteria();
    $c->setDistinct();
    $c->add(self::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());

    foreach (ShiftPeer::doSelect(new Criteria()) as $shift)
    {
      $ids = $shift->getStudentIdsFromDivisions(DivisionStudentPeer::doSelectDivisionsForCareerSchoolYearAndShift($career_school_year, $shift));
      $c->addAnd(self::STUDENT_ID, $ids, Criteria::NOT_IN);
    }
    return self::doCount($c, $con);
  }
  
  public static function getLastStudentCareerSchoolYear(Student $student, CareerSchoolYear $career_school_year)
  {
    $c = new Criteria();
    $c->add(self::STUDENT_ID, $student->getId());
    $c->addDescendingOrderByColumn(self::YEAR);

    return self::doSelectOne($c);
  }
  
  public static function getStudentsForYear($parameters)
  {
        $sy = SchoolYearPeer::retrieveCurrent();

        $c = new Criteria();
        $c->addJoin(self::STUDENT_ID, StudentPeer::ID);
        $c->addJoin(self::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
        $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID,$sy->getId());
        $c->add(self::YEAR,$parameters['academic_year']);
        

        $scsys = self::doSelect($c);

        if (!$scsys)
        {
          throw new sfError404Exception(sprintf(''));
        }

        return $scsys;
  }

  public static function retrieveByStudentAndCareerSchoolYear($student,$career_school_year) 
  {
        $c = new Criteria();
        $c->add(self::STUDENT_ID,$student->getId());
        $c->add(self::CAREER_SCHOOL_YEAR_ID,$career_school_year->getId());
        return  self::doSelectOne($c);
  }
  
  public static function retrieveByStudentAndYear($student,$year)
  {
      $c= new Criteria();
      $c->add(self::STUDENT_ID,$student->getId());
      $c->add(self::YEAR,$year);
      $c->add(self::STATUS, StudentCareerSchoolYearStatus::REPPROVED, Criteria::NOT_EQUAL);
      
      return self::doSelectOne($c);

  }

}
