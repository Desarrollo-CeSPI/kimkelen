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
  protected
  $_examination_number_short = array(
    self::DECEMBER => 'PEEE-dic',
    self::FEBRUARY => 'PEEE-feb',
  );
  
  const POSTPONED_NOTE = 6;

  public function getCourseSubjectStudentResult(CourseSubjectStudent $course_subject_student, PropelPDO $con = null)
  {
    if (!$course_subject_student->getConfiguration()->isNumericalMark())
    {
      $letter_average = LetterMarkAveragePeer::getLetterMarkAverageByCourseSubjectStudent($course_subject_student);
      $average = NULL;
      if(!is_null($letter_average))
     {

        $average = LetterMarkPeer::getLetterMarkByPk($letter_average->getLetterMarkAverage());
        $average = $average->getValue();
     }
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

  public function getColorForCourseSubjectStudentMark(CourseSubjectStudentMark $course_subject_student_mark)
  {
    if (! $course_subject_student_mark->getIsClosed() || is_null($course_subject_student_mark->getMark()))
    {
      return '';
    }

    if ($course_subject_student_mark->getMark() >= $this->getExaminationNote())
    {
      $class = 'mark_green';
    }
    else
    {
      $class = 'mark_red';
    }

    return $class;
  }


  /**
   * This method returns the marks average of a student in a course_subject_student given.
   *
   * @param CourseSubjectStudent $course_subject_student
   * @return <type>
   */
  public function getMarksAverage($course_subject_student, PropelPDO $con = null)
  {

      if (!is_null($course_subject_student->getStudentApprovedCourseSubject())) {
        $average = $course_subject_student->getStudentApprovedCourseSubject()->getMark();

      } else {
        $sum = 0;
        foreach ($course_subject_student->getCourseSubjectStudentMarks() as $cssm) {
          $sum += $cssm->getMark();
        }

        $average = (string) ($sum / $course_subject_student->countCourseSubjectStudentMarks(null, false, $con));
      }

    $average = sprintf('%.4s', $average);
    return $average;
  }
  /*
   * This method returns the marks average of a student between a course_subject_student and course_subject_student_examination
   * 
   * */
  public function getAverage($course_subject_student, $course_subject_student_examination)
  {
	  return $course_subject_student_examination->getMark();
 
  }
  
  public function getAnualAverageForStudentCareerSchoolYear($student_career_school_year)
  {
    if ($this->hasApprovedAllCourseSubjects($student_career_school_year))
    {
      $sum = 0;

      $course_subject_students = CourseSubjectStudentPeer::retrieveAverageableByCareerSchoolYearAndStudent(
        $student_career_school_year->getCareerSchoolYear(),
        $student_career_school_year->getStudent());

      foreach ($course_subject_students as $course_subject_student)
      {
          if($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration()->isNumericalMark())
              $sum += $course_subject_student->getFinalMark();
          else  
              return 0;
      }

      if (count($course_subject_students))
      {
        return round(($sum / count($course_subject_students)), 2);
      }
    }
    return null;

  }
  
  public function isApproved(CourseSubjectStudent $course_subject_student, $average, PropelPDO $con = null)
  {
    $minimum_mark = $course_subject_student->getCourseSubject($con)->getCareerSubjectSchoolYear($con)->getConfiguration($con)->getCourseMinimunMark();
    return $average >= $minimum_mark
      && $course_subject_student->getMarkFor($course_subject_student->countCourseSubjectStudentMarks(null, false, $con), $con)->getMark() >= self::POSTPONED_NOTE;

  }
  
  public function getExaminationNumberFor($average, $is_free = false, $course_subject_student = null)
  { //retorna siempre Diciembre.
    return self::DECEMBER;

  }

}
