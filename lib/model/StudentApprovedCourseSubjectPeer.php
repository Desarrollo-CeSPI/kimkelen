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

class StudentApprovedCourseSubjectPeer extends BaseStudentApprovedCourseSubjectPeer
{
	public static function retrieveForCourseSujectStudent($course_subject_student)
	{
		$c = new Criteria();
		$c->add(self::COURSE_SUBJECT_ID, $course_subject_student->getCourseSubjectId());
		$c->add(self::STUDENT_ID, $course_subject_student->getStudentId());

		return self::doSelectOne($c);
	}

  public static function retrieveByPStudentApprovedCareerSubjectId($student_approve_career_subject_id)
  {
    $c = new Criteria();
    $c->add(self::STUDENT_APPROVED_CAREER_SUBJECT_ID,$student_approve_career_subject_id);
    return self::doSelect($c);
  }
  
  public static function retrieveForCourseSujectStudentAndSchoolYearId($course_subject_student, $school_year_id)
  {
    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_ID, $course_subject_student->getCourseSubjectId());
    $c->add(self::STUDENT_ID, $course_subject_student->getStudentId());
    $c->add(self::SCHOOL_YEAR_ID, $school_year_id);
    return self::doSelectOne($c);
  }

  public static function retrieveByStudentApprovedCareerSubject($studentApprovedCareerSubject, $criteria = null)
  {
    if(is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->add(StudentApprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, $studentApprovedCareerSubject->getId());

    return StudentApprovedCourseSubjectPeer::doSelectOne($criteria);
  }
}