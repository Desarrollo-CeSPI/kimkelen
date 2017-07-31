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

class CourseSubjectPeer extends BaseCourseSubjectPeer {

  static public function getPotentialStudentIds($subject_id, $course_id, $potential_student_ids) {
    $criteria = new Criteria();
    $criteria->add(self::SUBJECT_ID, $subject_id, Criteria::EQUAL);
    $criteria->add(self::COURSE_ID, $course_id, Criteria::NOT_EQUAL);

    $same_student_ids = array();
    foreach (self::doSelect($criteria) as $course_subject) {
      $students = CourseStudentPeer::getStudentForCourse($course_subject->getCourseId());
      foreach ($students as $student) {
        if (in_array($student->getId(), $potential_student_ids)) {
          $same_student_ids[] = $student->getId();
        }
      }
    }
    $student_ids = array_diff($potential_student_ids, $same_student_ids);

    return $student_ids;
  }

  static public function retrieveByCareerYearAndSchoolYear($career, $year, $school_year) {
    $criteria = new Criteria();
    $criteria->add(CareerSubjectPeer::CAREER_ID, $career->getId());
    $criteria->add(CareerSubjectPeer::YEAR, $year);
    $criteria->addJoin(CareerSubjectPeer::ID, CourseSubjectPeer::CAREER_SUBJECT_ID, Criteria::INNER_JOIN);
    $criteria->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID, Criteria::INNER_JOIN);
    $criteria->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());

    return self::doSelect($criteria);
  }

  static public function retriveByCareerSubjectSchoolYearAndCourse($career_subject_school_year_id, $course_id) {
    $c = new Criteria();
    $c->add(self::CAREER_SUBJECT_SCHOOL_YEAR_ID, $career_subject_school_year_id);
    $c->add(self::COURSE_ID, $course_id);

    return self::doSelectOne($c);
  }

  static public function retrieveByCareerSubjectSchoolYear($career_subject_school_year_id) 
  {
    $c = new Criteria();
    $c->add(self::CAREER_SUBJECT_SCHOOL_YEAR_ID, $career_subject_school_year_id);

    return self::doSelect($c);
  }

  static public function retrieveByCourseId($course_id)
  {
    $c = new Criteria();
    $c->add(self::COURSE_ID, $course_id);

    return self::doSelectOne($c);
  }

  static public function retrieveCriteriaForCurrentYear($criteria)
  {
    if (is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->addJoin(self::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());

    return $criteria;
  }

}