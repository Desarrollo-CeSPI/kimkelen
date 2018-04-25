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
class FhaycsnormalEvaluatorBehaviour extends BaseEvaluatorBehaviour
{

  const POSTPONED_NOTE_FHAYCS = 5; //nota del 3er trimestre no puede ser menor de 6

  const DECEMBER = 1;
  const FEBRUARY = 2;
  const MARCH    = 3;

  protected
  $_examination_number = array(
    self::DECEMBER => 'Diciembre',
    self::FEBRUARY => 'Febrero',
    self::MARCH => 'Marzo',
  );

  protected
  $_examination_number_short = array(
    self::DECEMBER => 'Diciembre',
    self::FEBRUARY => 'Febrero',
    self::MARCH => 'Marzo',
  );


  public function getExaminationNumberFor($average, $is_free = false, $course_subject_student = null)
  {
    return self::DECEMBER; //Todos los alumnos se van a diciembre

  }

  public function getPosponedNote()
  {
    return self::POSTPONED_NOTE_FHAYCS;
  }

  public function getAverage($course_subject_student, $course_subject_student_examination)
  {
    $examination = $course_subject_student_examination->getExaminationSubject()->getExamination();
    #DICIEMBRE
    if ($examination->getExaminationNumber() == self::DECEMBER)
    {
      return $course_subject_student_examination->getMark();
    }
    elseif ($examination->getExaminationNumber() == self::FEBRUARY)
    {
      return $course_subject_student_examination->getMark();
    }
    elseif ($examination->getExaminationNumber() == self::MARCH)
    {
      return $course_subject_student_examination->getMark();
    }
    else
    {
      return (string) (($course_subject_student->getMarksAverage() + $course_subject_student_examination->getMark()) / 2);
    }
  }


  /**
   * If the student approves the previous, then it creates a student_approved_career_subject for this student
   *
   * @param StudentExaminationRepprovedSubject $student_examination_repproved_subject
   * @param PropelPDO $con
   */
  public function closeStudentExaminationRepprovedSubject(StudentExaminationRepprovedSubject $student_examination_repproved_subject, PropelPDO $con)
  {
    if ($student_examination_repproved_subject->getMark() >= $this->getExaminationNote())
    {
      $student_approved_career_subject = new StudentApprovedCareerSubject();
      $student_approved_career_subject->setCareerSubject($student_examination_repproved_subject->getExaminationRepprovedSubject()->getCareerSubject());
      $student_approved_career_subject->setStudent($student_examination_repproved_subject->getStudent());
      $student_approved_career_subject->setSchoolYear($student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getSchoolYear());

      //Final average in our fhaycs schools is the aprobed note, so the note in this case will be only the mark of student_examination_repproved_subject
      $average = (string) ($student_examination_repproved_subject->getMark());

      $average = sprintf('%.4s', $average);
      if ($average < self::MIN_NOTE)
      {
        $average = self::MIN_NOTE;
      }
      $student_approved_career_subject->setMark($average);

      $student_repproved_course_subject = $student_examination_repproved_subject->getStudentRepprovedCourseSubject();
      $student_repproved_course_subject->setStudentApprovedCareerSubject($student_approved_career_subject);
      $student_repproved_course_subject->save($con);

      ##se agrega el campo en student_disapproved_course_subject a el link del resultado final
      $student_repproved_course_subject->getCourseSubjectStudent()->getCourseResult()->setStudentApprovedCareerSubject($student_approved_career_subject)->save($con);

      $student_approved_career_subject->save($con);
    }

  }

  
  public function checkRepeationCondition(Student $student, StudentCareerSchoolYear $student_career_school_year)
  { // el nombre del método esta mal escrito

    $career_school_year = $student_career_school_year->getCareerSchoolYear();

    if ($student_career_school_year->isLastYear())
    {
      return false;
    }

    
    // Se quita por que no aplica nuestro establecimiento
    // $last_year_previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareerAndYear($student, $career_school_year->getCareer(), $student_career_school_year->getYear() - 1);
    // if ($last_year_previous > 0)
    // {
    //   return true;
    // }

    //If Previous count > max count of repproved subject allowed, then the student will repeat or go to pathways programs
    $previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareer($student, $student_career_school_year->getCareerSchoolYear()->getCareer());

    return ($previous > $career_school_year->getSubjectConfiguration()->getMaxPrevious());
  }

}