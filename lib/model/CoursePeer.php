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

class CoursePeer extends BaseCoursePeer
{

  /*
   * This static method returns the criteria, for the students that are inscripted in any subject of the course_id with status <> to withdrawn or withdrawn with reserve
   *
   * @return Criteria
   */

  static public function retrieveStudentsCriteria($course_id)
  {
    $course = CoursePeer::retrieveByPk($course_id);

    $c = new Criteria();
    $c->add(CourseSubjectPeer::COURSE_ID, $course_id);
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
    $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
    $c->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $c->addJoin(SchoolYearStudentPeer::STUDENT_ID, StudentPeer::ID);
    $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, CourseSubjectStudentPeer::STUDENT_ID, Criteria::INNER_JOIN);
    $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN, Criteria::NOT_EQUAL);
    $c->addAnd(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE, Criteria::NOT_EQUAL);
    $c->setDistinct();
    $c->add(SchoolYearStudentPeer::SCHOOL_YEAR_ID, $course->getSchoolYearId());
    return $c;

  }

  /*
   * This static method returns the criteria, for the course_subject_students that are related to any subject of the course_id
   *
   * @return Criteria
   */

  static public function retrieveCourseSubjectStudentsCriteria($course_id)
  {
    $c = new Criteria();
    $c->add(CourseSubjectPeer::COURSE_ID, $course_id);
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
    return $c;

  }

  static public function retrieveComissionsForSchoolYearCriteria(SchoolYear $school_year)
  {
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(CareerSchoolYearPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c->add(CoursePeer::DIVISION_ID, null, Criteria::ISNULL);

    return $c;

  }

  static public function retrieveComissionsForCareerSchoolYear(CareerSchoolYear $career_school_year)
  {
    $c = new Criteria();
    $c->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID, Criteria::INNER_JOIN);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $c->add(CoursePeer::DIVISION_ID, null, Criteria::ISNULL);
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());

    return self::doSelect($c);
  }

  static public function retrieveComissionsForSchoolYear(SchoolYear $school_year)
  {
    return self::doSelect(self::retrieveComissionsForSchoolYearCriteria($school_year));

  }

  static public function retrieveActiveComissions()
  {
    return self::retrieveComissionsForSchoolYear(SchoolYearPeer::retrieveCurrent());

  }

  static public function search($query_string, $sf_user)
  {
    if (strlen($query_string) > 3)
    {
      $c = new Criteria();
      $c->add(self::NAME, '%' . $query_string . '%', Criteria::LIKE);
      $c->add(self::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());

      if ($sf_user->isPreceptor())
      {
        PersonalPeer::joinWithCourse($c, $sf_user->getGuardUser()->getId(), true);
      }
      elseif ($sf_user->isTeacher())
      {
        TeacherPeer::joinWithCourses($c, $sf_user->getGuardUser()->getId());
      }

      return self::doSelect($c);
    }

    return array();

  }

  static public function sorted(Criteria $c)
  {
    //$c->addJoin(self::ID, CourseSubjectPeer::COURSE_ID, Criteria::INNER_JOIN);
    //$c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID, Criteria::INNER_JOIN);
    //$c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID, Criteria::INNER_JOIN);
    //$c->addAscendingOrderByColumn(CareerSubjectPeer::YEAR);
    $c->addAscendingOrderByColumn(self::NAME);

  }

  static public function retrieveComissionsForSchoolYearAndStudent($school_year, $student)
  {
    $c = self::retrieveComissionsForSchoolYearCriteria($school_year);
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    return self::doSelect($c);

  }

	/*
 * This static method returns the criteria, for the students that are inscripted in any subject of the course_id
 *
 * @return Criteria
 */

	static public function retrievePathwayStudentsCriteria($course_id)
	{
		$course = CoursePeer::retrieveByPk($course_id);

		$c = new Criteria();
		$c->add(CourseSubjectPeer::COURSE_ID, $course_id);
		$c->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPathwayPeer::COURSE_SUBJECT_ID);
		$c->addJoin(CourseSubjectStudentPathwayPeer::STUDENT_ID, StudentPeer::ID);
		$c->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
		$c->addJoin(SchoolYearStudentPeer::STUDENT_ID, StudentPeer::ID);
		$c->add(PersonPeer::IS_ACTIVE,true);
		$c->add(SchoolYearStudentPeer::SCHOOL_YEAR_ID, $course->getSchoolYearId());
		return $c;

	}

}
