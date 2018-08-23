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

class CareerStudentPeer extends BaseCareerStudentPeer
{
  /**
   * Answer an array of students that have relation with the career_id pass by parameter
   *
   * @param integer $career_id
   *
   * @return array
   */
  static public function retrieveStudentsForCareer($career_id)
  {
    $c = new Criteria();
    $c->add(self::CAREER_ID,$career_id);
    $c->addJoin(StudentPeer::ID,self::STUDENT_ID);
    return StudentPeer::doSelect($c);
  }

  /**
   * Answer and array of careers that have relation with the student_id pass by parameter
   *
   * @param integer $student_id
   *
   * @return array
   */
  static public function retrieveCareersForStudent($student_id)
  {
    $c = new Criteria();
    $c->add(self::STUDENT_ID,$student_id);
    $c->addJoin(self::CAREER_ID,CareerPeer::ID);
    return CareerPeer::doSelect($c);
  }

  /**
   * Answer if the student have and inscription in ther career, the count will be one or zero
   *
   * @param integer $career_id
   * @param integer $student_id
   *
   * @see retrieveByCareerAndStudent
   *
   * @return integer
   */
  static public function checkCareerStudentInscription($career_id, $student_id)
  {
    $career_student = self::retrieveByCareerAndStudent($career_id,$student_id);
    return count ($career_student) ;
  }

  /**
   * Answer a CareerStudent object for the career and student id's pass by parameter
   *
   * @param integer $career_id
   *
   * @param integer $student_id
   *
   * @return CareerStudent
   */
  static public function retrieveByCareerAndStudent ($career_id, $student_id)
  {
    $criteria = new Criteria();
    $criteria->add(self::CAREER_ID,$career_id);
    $criteria->add(self::STUDENT_ID,$student_id);

    $career_student = self::doSelectOne($criteria);

    self::clearInstancePool();
    unset($criteria);

    return $career_student;
  }
  static public function retrieveByStudent ($student_id)
  {
    $criteria = new Criteria();
    $criteria->add(self::STUDENT_ID,$student_id);
    $criteria->addDescendingOrderByColumn(self::CREATED_AT);

    return self::doSelectOne($criteria);
  }

  /**
   * Answer the count of CareerStudent for the career_ids and the student_id pass by parameter,
   * also check if the Student is in regular status in the career student object
   *
   * @param Course $course
   * @param Student $student
   * @param array $career_ids
   *
   * @return integer
   */
  static public function canRegisteredToCourse($course, $student, $career_ids)
  {
    $c = new Criteria();
    $c->add(CareerStudentPeer::STUDENT_ID, $student->getId());
    $c->add(CareerStudentPeer::CAREER_ID, $career_ids, Criteria::IN);
    $c->add(CareerStudentPeer::STATUS, CareerStudentStatus::CS_REGULAR);

    return self::doCount($c);
  }

  static public function doCountGraduatedForCareer($career, $c = null)
  {
    is_null($c)? $c = new Criteria(): $c;
    $c->add(CareerStudentPeer::CAREER_ID, $career->getId());
    $c->add(CareerStudentPeer::STATUS, CareerStudentStatus::GRADUATE);

    return self::doCount($c);
  }

  static public function doCountGraduatedForCareerSchoolYear($career_school_year)
  {
    $c = new Criteria();
    $c->addJoin(self::STUDENT_ID, StudentCareerSchoolYearPeer::STUDENT_ID);
    $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());

    return self::doCountGraduatedForCareer($career_school_year->getCareer(), $c);
  }

  public static function retrieveLastYearCareerGraduatedStudents($career_school_year)
  {
    $last_year_school_year = SchoolYearPeer::retrieveLastYearSchoolYear(SchoolYearPeer::retrieveCurrent());
    $c = new Criteria();

    $c->addJoin(self::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
    $c->add(self::STATUS, CareerStudentStatus::GRADUATE);
    $c->add(self::GRADUATION_SCHOOL_YEAR_ID, $last_year_school_year->getId());
    $c->add(self::CAREER_ID, $career_school_year->getCareer()->getId());

    return StudentPeer::doSelect($c);
  }

}