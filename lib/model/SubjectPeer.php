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

class SubjectPeer extends BaseSubjectPeer
{
  static public function doSelectOrdered(Criteria $criteria = null)
  {
    $criteria->addAscendingOrderByColumn(self::NAME);
    $criteria->setDistinct();
    return self::doSelect($criteria);
  }

  static public function doSelectOrderedAndActive(Criteria $criteria = null)
  {
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $criteria->addJoin(CareerSchoolYearPeer::CAREER_ID,CareerSubjectPeer::CAREER_ID);
    $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
    $criteria->setDistinct();
    $criteria->addAscendingOrderByColumn(self::NAME);
    return self::doSelect($criteria);
  }

  /**
   * This static method retrieves all the subjects for a course.
   *
   * @param Course $course
   * @return Criteria
   */
  static public function retrieveForCourseCriteria(Course $course)
  {
    $c = new Criteria();
    $c->add(CourseSubjectPeer::COURSE_ID, $course->getId());
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID,  CareerSubjectPeer::ID, Criteria::INNER_JOIN);
    $c->addJoin(CareerSubjectPeer::SUBJECT_ID, self::ID, Criteria::INNER_JOIN);
    $c->setDistinct();

    return $c;
  }

  /**
   *
   * @param Course $course
   * @return array Subejct[]
   */
  static public function retrieveForCourse(Course $course)
  {
    return self::doSelect(self::retrieveForCourseCriteria($course));
  }
  /**
   *
   * @param Course $course
   * @return integer
   */
  static public function countForCourse(Course $course)
  {
    return self::doCount(self::retrieveForCourseCriteria($course));
  }
}