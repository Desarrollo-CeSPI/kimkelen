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

/**
 * Copy and rename this class if you want to extend and customize
 */
class NacionalEvaluatorBehaviour extends BaseEvaluatorBehaviour
{
  /*
   * Returns if a student has approved or not the course subject
   *
   * @param CourseSubjectStudent $course_subject_student
   * @param PropelPDO $con
   *
   * @return Object $object
   */
  public function isApproved(CourseSubjectStudent $course_subject_student, $average, PropelPDO $con = null)
  {
	  $correct_last_note = true;
  	if (!(CourseType::BIMESTER == $course_subject_student->getCourseSubject()->getCourseType()))
  	{
		  $correct_last_note = $course_subject_student->getMarkFor($course_subject_student->countCourseSubjectStudentMarks(null, false, $con), $con)->getMark() >= self::POSTPONED_NOTE;
  	}

  	$minimum_mark = $course_subject_student->getCourseSubject($con)->getCareerSubjectSchoolYear($con)->getConfiguration($con)->getCourseMinimunMark();

    return ($average >=  $minimum_mark)  && $correct_last_note;
  }

  /**
   * This method check the conditions of repetition of a year.
   *
   * @param Student $student
   * @param StudentCareerSchoolYear $student_career_school_year
   * @return boolean
   */
  public function checkRepeationCondition(Student $student, StudentCareerSchoolYear $student_career_school_year)
  {
    //IF the current year is the last year of the career, the students not repeat. OR if the year  = 1
    if ($student_career_school_year->isLastYear() || $student_career_school_year->getYear() == 1)
    {
      return false;
    }

    return parent::checkRepeationCondition($student, $student_career_school_year);
  }

}