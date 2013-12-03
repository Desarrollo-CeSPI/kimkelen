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
class PellegriniEvaluatorBehaviour extends BaseEvaluatorBehaviour
{

  const NOVEMBER = 1;
  const DECEMBER = 2;
  const FEBRUARY = 3;
  const MARCH = 4;
  const FIRST_INTEGRATOR_MARK = 2; //nota del examen integrador
  const LAST_INTEGRATOR_MARK = 4; //nota del examen integrador
  const MIN_NOTE = 3;
  const MEDIUM_NOTE = 6;

  protected
  $_examination_number = array(
    self::NOVEMBER => 'Noviembre',
    self::DECEMBER => 'Diciembre',
    self::FEBRUARY => 'Febrero',
    self::MARCH => 'Marzo',
  );
  protected
  $_examination_number_short = array(
    self::NOVEMBER => 'Rec Diciembre',
    self::DECEMBER => 'Diciembre',
    self::FEBRUARY => 'Febrero',
    self::MARCH => 'Marzo',
  );

  public function isApproved(CourseSubjectStudent $course_subject_student, $average, PropelPDO $con = null)
  {
    $sum = 0;
    foreach ($course_subject_student->getCourseSubjectStudentMarks(null, $con) as $cssm)
    {
      $sum += $cssm->getMark();
    }
    $first_integrator_mark = $course_subject_student->getMarkFor(self::FIRST_INTEGRATOR_MARK, $con);
    $last_integrator_mark = $course_subject_student->getMarkFor(self::LAST_INTEGRATOR_MARK, $con);


    return ($sum > 26
      && isset($first_integrator_mark)
      && $first_integrator_mark->getMark() >= $course_subject_student->getCourseMinimunMarkForCurrentSchoolYear($con)
      && isset($last_integrator_mark)
      && $course_subject_student->getMarkFor(self::LAST_INTEGRATOR_MARK, $con)->getMark() >= $course_subject_student->getCourseMinimunMarkForCurrentSchoolYear($con));

  }

  public function getExaminationNumberFor($average, $is_free = false, $course_subject_student = null)
  {
    return (($average >= self::MIN_NOTE)) ? self::NOVEMBER : self::FEBRUARY;

  }

  public function nextCourseSubjectStudentExamination($course_subject_student_examination, $con)
  {
    $new_course_subject_student_examination = $course_subject_student_examination->copy();
    if ($course_subject_student_examination->getExaminationNumber() == self::NOVEMBER)
    {
      $course_subject_student_examination->getMark() > self::MIN_NOTE ? $new_course_subject_student_examination->setExaminationNumber(self::DECEMBER) : $new_course_subject_student_examination->setExaminationNumber(self::FEBRUARY);
    }
    elseif ($course_subject_student_examination->getExaminationNumber() == self::DECEMBER)
    {
      $new_course_subject_student_examination->setExaminationNumber(self::FEBRUARY);
    }
    elseif ($course_subject_student_examination->getExaminationNumber() == self::FEBRUARY)
    {
      $new_course_subject_student_examination->setExaminationNumber(self::MARCH);
    }
    $new_course_subject_student_examination->setMark(null);
    $new_course_subject_student_examination->setExaminationSubjectId(null);
    $new_course_subject_student_examination->setIsAbsent(false);
    $new_course_subject_student_examination->save($con);

  }

  public function getExaminationResult(CourseSubjectStudentExamination $css_examination)
  {
    if ($css_examination->getMark())
    {
      if ($css_examination->getMark() < self::EXAMINATION_NOTE)
      {
        if ($css_examination->getExaminationNumber() < count($this->_examination_number))
        {
          if ($css_examination->getMark() < self::MIN_NOTE && $css_examination->getExaminationNumber() == self::NOVEMBER)
          {
            $current = $this->_examination_number[$css_examination->getExaminationNumber()];
            $next = $this->_examination_number[$css_examination->getExaminationNumber() + 2];
            return array(strtolower($current), $next);
          }
          else
          {
            $current = $this->_examination_number[$css_examination->getExaminationNumber()];
            $next = $this->_examination_number[$css_examination->getExaminationNumber() + 1];
            return array(strtolower($current), $next);
          }
        }
        else
        {
          return array(strtolower($this->_examination_number[$css_examination->getExaminationNumber()]), "Previous");
        }
      }
      else
      {
        return array("approved", "Approved");
      }
    }
    else
    {
      return array("absent", $css_examination->getExaminationNumber() < count($this->_examination_number) ? $this->_examination_number[$css_examination->getExaminationNumber() + 1] : "Previous");
    }

  }

  public function createStudentApprovedCourseSubject($course_subject_student, $average, $con)
  {
    if ($average < 7)
      $average = 7;
    parent::createStudentApprovedCourseSubject($course_subject_student, $average, $con);

  }

    /**
   * If the student approves the previous, then it creates a student_approved_career_subject for this student
   *
   * @param StudentExaminationRepprovedSubject $student_examination_repproved_subject
   * @param PropelPDO $con
   */
  public function closeStudentExaminationRepprovedSubject(StudentExaminationRepprovedSubject $student_examination_repproved_subject, PropelPDO $con)
  {
    if ($student_examination_repproved_subject->getMark() >= self::EXAMINATION_NOTE)
    {
      $student_approved_career_subject = new StudentApprovedCareerSubject();
      $student_approved_career_subject->setCareerSubject($student_examination_repproved_subject->getExaminationRepprovedSubject()->getCareerSubject());
      $student_approved_career_subject->setStudent($student_examination_repproved_subject->getStudent());
      $student_approved_career_subject->setSchoolYear($student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getSchoolYear());

      //Final average is directly student_examination_repproved_subject mark
      $mark = (string) ($student_examination_repproved_subject->getMark());

      $mark = sprintf('%.4s', $mark);
      if ($mark < self::MIN_NOTE)
      {
        $mark = self::MIN_NOTE;
      }
      $student_approved_career_subject->setMark($mark);

      $student_repproved_course_subject = $student_examination_repproved_subject->getStudentRepprovedCourseSubject();
      $student_repproved_course_subject->setStudentApprovedCareerSubject($student_approved_career_subject);
      $student_repproved_course_subject->save($con);


      $student_repproved_course_subject->getCourseSubjectStudent()->getCourseResult()->setStudentApprovedCareerSubject($student_approved_career_subject)->save($con);

      $student_approved_career_subject->save($con);
    }
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
    //If current year is the last year of the career
    if ($student_career_school_year->isLastYear())
    {
      return false;
    }

    $career_school_year = $student_career_school_year->getCareerSchoolYear();

    //IF Previous is > than max count of previous allowed, then student repeats year
    $previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareer($student, $student_career_school_year->getCareerSchoolYear()->getCareer());

    return ($previous > $career_school_year->getSubjectConfiguration()->getMaxPrevious());
  }
}