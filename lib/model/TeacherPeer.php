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

class TeacherPeer extends BaseTeacherPeer
{
  /**
   * Retrieves an array of active Personal and ordered by Lastname.
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
   * This method joins the teacher indicated with the corresponing students.
   *
   * @return $criteria
   */
  public static function joinWithStudents(Criteria $c , $user_id)
  {
    $c1 = new Criteria();
    $c1->add(PersonPeer::USER_ID, $user_id);
    $c1->addJoin(PersonPeer::ID, TeacherPeer::PERSON_ID);
    $c1->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
    $c1->addJoin(CourseSubjectTeacherPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c1->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
    $c1->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);

    $c1->clearSelectColumns();
    $c1->addSelectColumn(StudentPeer::ID);
    $c1->setDistinct();
    $stmt = StudentPeer::doSelectStmt($c1);
    $student_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $c->add(StudentPeer::ID, $student_ids, Criteria::IN);
  }

  /**
   * This method joins to the courses of the teacher
   * @param Criteria $c
   * @param <type> $user_id
   */
  public static function joinWithCourses(Criteria $criteria , $user_id, $only_commisions = false)
  {
    if ($only_commisions)
    {
      $criteria->add(CoursePeer::DIVISION_ID, null, Criteria::ISNULL);
    }

    $criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
    $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID);
    $criteria->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
    $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::USER_ID, $user_id);
    $criteria->setDistinct();
    
  }

  /**
   * This method joins to the divisions of the teacher
   * @param Criteria $c
   * @param <type> $user_id
   */
  public static function joinWithDivisions(Criteria $criteria , $user_id)
  {
    $criteria->setDistinct(DivisionPeer::ID);
    $criteria->addJoin(DivisionPeer::ID, CoursePeer::DIVISION_ID);
    $criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
    $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID);
    $criteria->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
    $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::USER_ID, $user_id);
  }

}