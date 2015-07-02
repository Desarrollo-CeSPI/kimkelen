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

class PersonalPeer extends BasePersonalPeer
{
  /**
   * Retrieves an array of active Personal.
   * @param Criteria $criteria
   * @param PropelPDO $con
   * @return <array>  Personal[]
   */
  public static function doSelectActive(Criteria $criteria, PropelPDO $con = null)
  {
    $criteria = PersonPeer::doSelectOrderedCriteria($criteria, $con);
    $criteria->add(PersonPeer::IS_ACTIVE, true);
    $criteria->addJoin(PersonPeer::ID, self::PERSON_ID);

    return self::doSelect($criteria, $con);
  }

  /**
   * This method receives a Criteria and adds the joins necesary to join a preceptor with his course.
   * The param add_division_courses, decides if the method add the courses that have divisions or not (only commissions by default)
   *
   * @param Criteria $criteria
   * @param integer $user_id
   * @param boolean $add_division_courses
   *
   * @return Criteria
   */
  public static function joinWithCourse(Criteria $criteria, $user_id, $add_division_courses = false)
  {
    $course_ids = self::retrieveCourseIdsjoinWithDivisionCourseOrCommission($user_id, $add_division_courses);
    $criteria->add(CoursePeer::ID, $course_ids, Criteria::IN);
    return $criteria;
  }

  /**
   * This method receives a Criteria and adds the joins necesary to join a preceptor with his division courses or commissions.
   *
   * @param integer $user_id
   *
   * @return Criteria
   */
  public static function retrieveCourseIdsjoinWithDivisionCourseOrCommission( $user_id, $add_division_courses = false)
  {
    $preceptor = self::retrievePreceptorBySfGuardUserId($user_id);
    if (is_null($preceptor))
    {
      return array();
    }
    $c1 = new Criteria();
    $c1->addJoin(CoursePeer::ID, CoursePreceptorPeer::COURSE_ID);
    $c1->add(CoursePreceptorPeer::PRECEPTOR_ID, $preceptor->getId());
    $c1->clearSelectColumns();
    $c1->addSelectColumn(CoursePeer::ID);
    $stmt = CoursePeer::doSelectStmt($c1);
    $course_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($add_division_courses)
    {
      $c2 = new Criteria();
      $c2->addJoin(CoursePeer::DIVISION_ID, DivisionPeer::ID);
      $c2->addJoin(DivisionPeer::ID, DivisionPreceptorPeer::DIVISION_ID);
      $c2->add(DivisionPreceptorPeer::PRECEPTOR_ID, $preceptor->getId());
      $c2->clearSelectColumns();
      $c2->addSelectColumn(CoursePeer::ID);
      $stmt = CoursePeer::doSelectStmt($c2);

      $course_ids = array_merge($course_ids,$stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    return $course_ids;
  }

  /**
   * This method joins the preceptor indicated with the corresponing students.
   *
   * @return $criteria
   */
  public static function joinWithStudents(Criteria $c , $user_id, $return_ids = false)
  {
    $c1 = new Criteria();
    $c1->add(PersonPeer::USER_ID, $user_id);
    $c1->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
    $c1->addJoin(PersonalPeer::ID,DivisionPreceptorPeer::PRECEPTOR_ID);
    $c1->addJoin(DivisionPreceptorPeer::DIVISION_ID, DivisionStudentPeer::DIVISION_ID);

    $c1->clearSelectColumns();
    $c1->addSelectColumn(DivisionStudentPeer::STUDENT_ID);
    $c1->setDistinct();
    $stmt = DivisionStudentPeer::doSelectStmt($c1);
    $division_in = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $c2 = new Criteria();
    self::joinWithCourse($c2, $user_id);
    $c2->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c2->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
    $c2->clearSelectColumns();
    $c2->addSelectColumn(CourseSubjectStudentPeer::STUDENT_ID);
    $c2->setDistinct();
    $stmt = DivisionStudentPeer::doSelectStmt($c2);
    $course_in = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $in = array_merge($course_in, $division_in);

    if ($return_ids)
      return $in;

    $c->add(StudentPeer::ID, $in, Criteria::IN);
    $c->setDistinct();
  }

  public static function doSelectActivePreceptor(Criteria $criteria = null)
  {
    $criteria = is_null($criteria)? new Criteria() : $criteria;
    $criteria->add(PersonalPeer::PERSONAL_TYPE, PersonalType::PRECEPTOR);
    PersonPeer::doSelectOrderedCriteria($criteria);
    $criteria->add(PersonPeer::IS_ACTIVE, true);
    $criteria->addJoin(PersonPeer::ID, self::PERSON_ID);

    $sf_user = sfContext::getInstance()->getUser();

    if ($sf_user->isHeadPreceptor())
    {
      $personal_in = $sf_user->getPersonalIds();
      $criteria->add(self::ID, $personal_in, Criteria::IN);
    }
    return self::doSelect($criteria);
  }

  public static function  retrievePreceptorBySfGuardUserId($sf_guard_user_id)
  {
    $criteria = new Criteria();
    $criteria->add(PersonPeer::USER_ID, $sf_guard_user_id);
    $criteria->addJoin(PersonPeer::ID, PersonalPeer::PERSON_ID);
    $criteria->add(PersonalPeer::PERSONAL_TYPE, PersonalType::PRECEPTOR);
    return PersonalPeer::doSelectOne($criteria);
  }

  static public function joinWithDivisions($c, $user_id)
  {
    $c->add(PersonPeer::USER_ID, $user_id);
    $c->addJoin(PersonPeer::ID , self::PERSON_ID);
    $c->addJoin(DivisionPreceptorPeer::PRECEPTOR_ID, self::ID);
    $c->addJoin(DivisionPreceptorPeer::DIVISION_ID, DivisionPeer::ID);
  }
}