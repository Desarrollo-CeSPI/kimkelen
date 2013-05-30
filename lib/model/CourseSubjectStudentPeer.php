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

class CourseSubjectStudentPeer extends BaseCourseSubjectStudentPeer
{

  static public function retrieveCourseSubjectsForStudent($student_id)
  {
    $criteria = new Criteria();
    $criteria->add(self::STUDENT_ID, $student_id);

    return self::doSelect($criteria);

  }

  public static function retrieveByCareerSchoolYearAndStudentAndCourseType(CareerSchoolYear $career_school_year, Student $student, $course_type, PropelPDO $con = null, $approved = false)
  {
    if (is_null($con))
    {
      $con = Propel::getConnection();
    }
    $c = new Criteria();
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectPeer::ID, self::COURSE_SUBJECT_ID);

    #agregado para el Course Tipe
    $c->addJoin(CareerSubjectSchoolYearPeer::SUBJECT_CONFIGURATION_ID, SubjectConfigurationPeer::ID);
    $c->add(SubjectConfigurationPeer::COURSE_TYPE, $course_type);
    $c->add(self::STUDENT_ID, $student->getId());

    if ($approved)
      $c->add(self::STUDENT_APPROVED_COURSE_SUBJECT_ID, null, Criteria::ISNOTNULL);
    $c->addGroupByColumn(self::COURSE_SUBJECT_ID);

    return self::doSelect($c, $con);

  }

  public static function retrieveCriteriaByCareerSchoolYearAndStudent(CareerSchoolYear $career_school_year, Student $student, PropelPDO $con = null, $approved = false)
  {
    if (is_null($con))
    {
      $con = Propel::getConnection();
    }

    $c = new Criteria();
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectPeer::ID, self::COURSE_SUBJECT_ID);
    $c->add(self::STUDENT_ID, $student->getId());

    if ($approved)
      $c->add(self::STUDENT_APPROVED_COURSE_SUBJECT_ID, null, Criteria::ISNOTNULL);

    $c->addGroupByColumn(self::COURSE_SUBJECT_ID);

    return $c;

  }

  public static function retrieveByCareerSchoolYearAndStudent(CareerSchoolYear $career_school_year, Student $student, PropelPDO $con = null, $approved = false)
  {
    $c = self::retrieveCriteriaByCareerSchoolYearAndStudent($career_school_year, $student, $con, $approved);

    return self::doSelect($c, $con);

  }

  public static function retrieveByCourseSubjectAndStudent($course_subject_id, $student_id)
  {
    $c = new Criteria();
    $c->add(self::STUDENT_ID, $student_id);
    $c->add(self::COURSE_SUBJECT_ID, $course_subject_id);

    return self::doSelectOne($c);

  }

  public static function retrieveByCourseSubject($course_subject_id)
  {
    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_ID, $course_subject_id);

    return self::doSelect($c);

  }

  public static function retrieveByCareerSubjectSchoolYearAndStudent(CareerSubjectSchoolYear $career_subject_school_year, $student_id)
  {
    $c = new Criteria();
    $c->add(self::STUDENT_ID, $student_id);
    $c->addJoin(self::COURSE_SUBJECT_ID, CourseSubjectPeer::ID, Criteria::INNER_JOIN);
    $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $career_subject_school_year->getId());

    return self::doSelectOne($c);
  }



  public static function retrieveByStudentApprovedCareerSubject($student_approved_career_subject, $school_year = null)
  {
    $career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByCareerSubjectAndSchoolYear($student_approved_career_subject->getCareerSubject(), $school_year);
    
    return self::retrieveByCareerSubjectSchoolYearAndStudent($career_subject_school_year, $student_approved_career_subject->getStudentId());
  }
}