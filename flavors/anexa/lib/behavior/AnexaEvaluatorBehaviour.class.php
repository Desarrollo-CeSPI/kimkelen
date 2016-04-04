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
class AnexaEvaluatorBehaviour extends BaseEvaluatorBehaviour
{

  public function getCourseSubjectStudentResult(CourseSubjectStudent $course_subject_student, PropelPDO $con = null)
  {
    if (!$course_subject_student->getConfiguration()->isNumericalMark())
    {
      $letter_average = LetterMarkAveragePeer::getLetterMarkAverageByCourseSubjectStudent($course_subject_student);
      $average = LetterMarkPeer::getLetterMarkByPk($letter_average->getLetterMarkAverage());
      $average = $average->getValue();
    }
    else
    {
      $average = $course_subject_student->getMarksAverage($con);
    }

    if ($this->isApproved($course_subject_student, $average, $con))
    {
      return $this->createStudentApprovedCourseSubject($course_subject_student, $average, $con);
    }
    else
    {
      $student_disapproved_course_subject = new StudentDisapprovedCourseSubject();
      $student_disapproved_course_subject->setCourseSubjectStudent($course_subject_student);
      $student_disapproved_course_subject->setExaminationNumber($this->getExaminationNumberFor($average, false, $course_subject_student));
      
      return $student_disapproved_course_subject;
    }

  }

  public function getExaminationNumberFor($average, $is_free = false, $course_subject_student = null)
  {
    // en graduada solo existe una mesa y se utiliza la de febrero.
    return self::FEBRUARY;

  }

}
