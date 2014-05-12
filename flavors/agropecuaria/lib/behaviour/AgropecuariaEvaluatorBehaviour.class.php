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
  const EXEMPT = 'Ex.';

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
    if ($examination->getExaminationNumber() == self::DECEMBER)
    {
      return (string) (($course_subject_student->getMarksAverage() + $course_subject_student_examination->getMark()) / 2);
    }
    elseif ($examination->getExaminationNumber() == self::FEBRUARY)
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

  public function closeCourseSubjectStudentExamination(CourseSubjectStudentExamination $course_subject_student_examination, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $course_subject_student = $course_subject_student_examination->getCourseSubjectStudent();

    // si aprueba la mesa de examen

    if ($course_subject_student_examination->getMark() >= $this->getExaminationNote())
    {
      $result = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student, $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear());

      if (is_null($result)){
        $result = new StudentApprovedCareerSubject();
        $result->setCareerSubject($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject());
        $result->setStudent($course_subject_student->getStudent());
        $result->setSchoolYear($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear());
      }

      $examination_subject = $course_subject_student_examination->getExaminationSubject();

      // IF is null, is because the course_subject_student_examination has been created editing student history
      $school_year = is_null($examination_subject) ? $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear() : $examination_subject->getExamination()->getSchoolYear();

      $result->setSchoolYearId($school_year->getId());

      $average = $this->getAverage($course_subject_student, $course_subject_student_examination);

      $average = sprintf('%.4s', $average);

      if ($average < 4)
      {
        $average = 4;
      }

      // se guarda la NOTA FINAL de la materia
      if ($course_subject_student_examination->getExaminationNumber() == self::FEBRUARY)
      {

        $this->setFebruaryApprovedResult($result, $average, $course_subject_student_examination->getMark());
      }
      else
      {
        $result->setMark($average);
      }

      ##se agrega en la tupla student_disapproved_course_subject el link a al resultado final
      $course_subject_student->getCourseResult()->setStudentApprovedCareerSubject($result)->save($con);

      $result->save($con);

      // verifica si hay creada una tupla de previa de esta materia que no corresponde porque el alumno aprobo en mesas regulares.
      // Si la encuentra la borra ya que no corresponde que exista
      $srcs = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student);
      if ($srcs && is_null($srcs->getStudentApprovedCareerSubject())) {
        $srcs->delete($con);
      }
    }
    else
    {
      // TODO: arreglar esto: pedir a la configuración
      // Pasa de diciembre a febrero (se copia el course_subject_student_examination con examination_number + 1)
      if ($course_subject_student_examination->getExaminationNumber() < count($this->_examination_number))
      {
        $this->nextCourseSubjectStudentExamination($course_subject_student_examination, $con);
      }
      else
      {
        // se crea una previa
        $srcs = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student);
        if (!$srcs && is_null($srcs->getStudentApprovedCareerSubject())) {
           $student_repproved_course_subject = new StudentRepprovedCourseSubject();
           $student_repproved_course_subject->setCourseSubjectStudentId($course_subject_student->getId());
           $student_repproved_course_subject->save($con);
        }
      }
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
    //IF the current year is the last year of the career.
    if ($student_career_school_year->isLastYear())
    {
      return false;
    }

    $career_school_year = $student_career_school_year->getCareerSchoolYear();

    //If previous count > than max count of previous allowed, then the student repeats
    $previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareer($student, $student_career_school_year->getCareerSchoolYear()->getCareer());

    return ($previous > $career_school_year->getSubjectConfiguration()->getMaxPrevious());
  }

   public function getExemptString()
  {
    return self::EXEMPT;
  }

}