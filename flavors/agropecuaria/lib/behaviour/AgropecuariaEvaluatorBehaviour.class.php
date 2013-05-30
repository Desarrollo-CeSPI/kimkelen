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

class AgropecuariaEvaluatorBehaviour extends BaseEvaluatorBehaviour
{

  protected
  $_examination_number_short = array(
    self::DECEMBER => 'Diciembre',
    self::FEBRUARY => 'Febrero',
  );

  const POSTPONED_NOTE = 6;
  const PROMOTION_NOTE = 6;

  /*
   * Returns if a student has approved or not the course subject
   *
   * @param CourseSubjectStudent $course_subject_student
   * @param PropelPDO $con
   *
   * @return Object $object
   */

  public function getCourseSubjectStudentResult(CourseSubjectStudent $course_subject_student, PropelPDO $con = null)
  {
    $average = $course_subject_student->getMarksAverage($con);
    if ($average >= $course_subject_student->getCourseSubject($con)->getCareerSubjectSchoolYear($con)->getConfiguration($con)->getCourseMinimunMark()
      && $course_subject_student->getMarkFor($course_subject_student->countCourseSubjectStudentMarks(null, false, $con), $con)->getMark() >= self::POSTPONED_NOTE)
    {
      $school_year = $course_subject_student->getCourseSubject($con)->getCourse($con)->getSchoolYear($con);

      $student_approved_course_subject = new StudentApprovedCourseSubject();
      $student_approved_course_subject->setCourseSubject($course_subject_student->getCourseSubject($con));
      $student_approved_course_subject->setStudent($course_subject_student->getStudent($con));
      $student_approved_course_subject->setSchoolYear($school_year);
      $student_approved_course_subject->setMark($average);
      $course_subject_student->setStudentApprovedCourseSubject($student_approved_course_subject);

      ###Liberando memoria ####
      $school_year->clearAllReferences(true);
      unset($school_year);
      SchoolYearPeer::clearInstancePool();
      unset($average);
      ##########################

      return $student_approved_course_subject;
    }
    else
    {
      $student_disapproved_course_subject = new StudentDisapprovedCourseSubject();
      $student_disapproved_course_subject->setCourseSubjectStudent($course_subject_student);

      if ($course_subject_student->hasSomeMarkFree())
      {
        $examination_number = self::DECEMBER;
      }
      else
      {
        $examination_number = $this->getExaminationNumberFor($average);
      }

      $student_disapproved_course_subject->setExaminationNumber($examination_number);

      unset($average);

      return $student_disapproved_course_subject;
    }

  }

  public function getAverage($course_subject_student, $course_subject_student_examination)
  {
    $examination = $course_subject_student_examination->getExaminationSubject()->getExamination();
    #DICIEMBRE
    if ($examination->getExaminationNumber() == 1)
    {
      return (string) (($course_subject_student->getMarksAverage() + $course_subject_student_examination->getMark()) / 2);
    }
    elseif ($examination->getExaminationNumber() == 2)
    {
      return $course_subject_student_examination->getMark();
    }
    else
    {
      return (string) (($course_subject_student->getMarksAverage() + $course_subject_student_examination->getMark()) / 2);
    }

  }

  public function getPartialAverageForQuaterly($course_subject_students, $number)
  {
    $partial_avg = 0;

    foreach ($course_subject_students as $course_subject_student)
    {
      if (!is_null($course_subject_student->getMarkForIsClose($number)))
      {
        $partial_avg = bcadd($partial_avg, $course_subject_student->getMarkForIsClose($number)->getMark(), 2);
      }
    }
    return bcdiv($partial_avg, count($course_subject_students), 2);
  }

  public function getPartialAverage($course_subject_students)
  {
    $partial_avg = 0;

    foreach ($course_subject_students as $course_subject_student)
    {
      $last_mark_is_closed = $course_subject_student->getLastMarkForIsClose();
      $partial_avg = bcadd($partial_avg, $last_mark_is_closed->getMark(), 2);
    }

    return bcdiv($partial_avg, count($course_subject_students), 2);
  }

  public function getPartialAverageForBimester($course_subject_students)
  {
    $partial_avg = 0;
    foreach ($course_subject_students as $course_subject_student)
    {
      $partial_avg = bcadd($partial_avg, $course_subject_student->getMarkFor(1));
    }
    return bcdiv($partial_avg, count($course_subject_students), 2);

  }

    public function getPromotionNote()
  {
    return self::PROMOTION_NOTE;
  }

}