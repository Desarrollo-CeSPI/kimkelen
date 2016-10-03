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

class StudentPeer extends BaseStudentPeer
{

  static public function getForDocumentTypeAndNumber($parameters)
  {
    $c = new Criteria();
    $c->addJoin(PersonPeer::ID, self::PERSON_ID, Criteria::INNER_JOIN);
    $c->add(PersonPeer::IDENTIFICATION_NUMBER, $parameters['document_number']);
    $c->add(PersonPeer::IDENTIFICATION_TYPE, $parameters['document_type']);
    $s = self::doSelectOne($c);
    if (!$s)
    {
      throw new sfError404Exception(sprintf('Student with document "%s" "%s" does not exist.',  $parameters['document_type'], $parameters['document_number']));
    }

    return $s;
  }


  static public function search($query_string, $sf_user, $limit = 12)
  {

    $query_string = trim($query_string);

    if (!is_null($query_string) && !empty($query_string) && '' != $query_string && strlen($query_string) > 3)
    {
      $criteria = new Criteria();
      $criteria->setIgnoreCase(true);
      $criteria->setLimit($limit);
      $criteria->add(SchoolYearStudentPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
      $criteria->addJoin(SchoolYearStudentPeer::STUDENT_ID, self::ID);
      $criteria->addJoin(self::PERSON_ID, PersonPeer::ID, Criteria::INNER_JOIN);
      $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);

      if (is_numeric($query_string))
      {
        // Search by identification number
        $criteria->add(PersonPeer::IDENTIFICATION_NUMBER, $query_string . '%', Criteria::LIKE);
      }
      else
      {
        // Search by firstname or lastname
        $criterion = $criteria->getNewCriterion(PersonPeer::FIRSTNAME, $query_string . '%', Criteria::LIKE);
        $criterion->addOr($criteria->getNewCriterion(PersonPeer::LASTNAME, $query_string . '%', Criteria::LIKE));
        $criteria->add($criterion);
      }

      if ($sf_user->isPreceptor())
      {
        PersonalPeer::joinWithStudents($criteria, $sf_user->getGuardUser()->getId());
      }
      elseif ($sf_user->isTeacher())
      {
        TeacherPeer::joinWithStudents($criteria, $sf_user->getGuardUser()->getId());
      }
      //FALTA HEAD PRECEPTOR

      return self::doSelectActive($criteria);
    }

    return array();

  }

  public static function getNextFileNumber()
  {
    $file_number = 1;
    $c = new Criteria();
    $c->addDescendingOrderByColumn("CONVERT(" . self::FILE_NUMBER . ", SIGNED INTEGER)");
    if ($obj = self::doSelectOne($c))
    {
      $file_number = $obj->getFileNumber() + 1;
    }

    return $file_number;

  }

  /*
   * This method is overwritten because cross modules filters cause problems.
   */

  public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
  {
    $criteria->addGroupByColumn(self::ID);

    return parent::doCount($criteria, $distinct, $con);

  }

  /**
   * Retrieves an array of active Personal.
   * @param Criteria $criteria
   * @param PropelPDO $con
   * @return <array>  Student[]
   */
  public static function doSelectActive(Criteria $criteria, PropelPDO $con = null)
  {
    $criteria = PersonPeer::doSelectOrderedCriteria($criteria, $con);
    $criteria->add(PersonPeer::IS_ACTIVE, true);
    $criteria->addJoin(PersonPeer::ID, self::PERSON_ID);

    return self::doSelect($criteria, $con);

  }

  public static function getStudentsForAttendance($sf_user, $division_id, $course_subject_id = null, $career_school_year_id = null)
  {
    $c = new Criteria();


    $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);


    $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID, self::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    if (!is_null($course_subject_id))
    {
      $c->add(CourseSubjectPeer::ID, $course_subject_id);
    }

    $c->add(CoursePeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $c->setDistinct();

    if ($division_id)
    {
      $c->add(CoursePeer::DIVISION_ID, $division_id);
    }

    if ($sf_user->isPreceptor())
    {
      $course_ids = PersonalPeer::retrieveCourseIdsjoinWithDivisionCourseOrCommission($sf_user->getGuardUser()->getId(), true);
      $c->add(CoursePeer::ID, $course_ids, Criteria::IN);
    }
    else
    {
      $c->addJoin(PersonPeer::ID, self::PERSON_ID);
    }

    return self::doSelectActive($c);

  }

  static public function sorted(Criteria $c = null)
  {
    if (is_null($c))
    {
      $c = new Criteria();
    }

    $c->addJoin(self::PERSON_ID, PersonPeer::ID);
    $c->addAscendingOrderByColumn(PersonPeer::LASTNAME);
    $c->addAscendingOrderByColumn(PersonPeer::FIRSTNAME);

    return self::doSelect($c);

  }

  public static function getCoursesForStudents($students_ids)
  {
    #Falta el año electivo actual
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $students_ids, Criteria::IN);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c->setDistinct(CoursePeer::ID);
    return CoursePeer::doSelect($c);

  }

  public static function getCourseSubjectForStudents($students_ids)
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $students_ids, Criteria::IN);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->setDistinct(CourseSubjectPeer::ID);
    return CourseSubjectPeer::doSelect($c);

  }

  public static function retrieveForCareerSchoolYearAndYear(CareerSchoolYear $career_school_year, $year)
  {
    $c = new Criteria();
    $c->addJoin(self::ID, StudentCareerSchoolYearPeer::STUDENT_ID);
    $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $c->add(StudentCareerSchoolYearPeer::YEAR, $year);

    return self::doSelect($c);
  }

}