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

class StudentCareerSchoolYear extends BaseStudentCareerSchoolYear
{

  /**
   * Returns the array with CourseSubjecStudent.
   *
   * Posiciones del vector ['ANUAL']['QUATERLY']['BIMESTER'], no necesariamente se cargan todos..
   * El mismo vector, devuelve la cantidad de notas maximas en cada arreglo.
   * @return array
   */
  public function getCourses()
  {
    $css = CourseSubjectStudentPeer::retrieveByCareerSchoolYearAndStudent($this->getCareerSchoolYear(), $this->getStudent());
    $course_subject_per_type = array();
      $anual_max = 0;
      $quaterly_max = 0;
      $bimester_max = 0;
      $quaterly_of_a_term_max = 0;
      $anual_period_max = 1;
      $quaterly_period_max = 1;
      $bimester_period_max = 1;
      $quaterly_of_a_term_period_max = 1;
    foreach ($css as $course_subject_student)
    {
      $course_type = $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration()->getCourseType();
      $course_type;
      switch ($course_type)
      {
        case 1:
          $course_subject_per_type['ANUAL'][] = $course_subject_student;
          $course_subject_per_type['ANUAL']['marks'] = max($anual_max, $course_subject_student->countCourseSubjectStudentMarks());
          $course_subject_per_type['ANUAL']['periods'] = max($anual_period_max, $course_subject_student->countCourseSubjectStudentPeriods());
          $anual_max       =($anual_max > $course_subject_student->countCourseSubjectStudentMarks())?$anual_max:$course_subject_student->countCourseSubjectStudentMarks();
          $anual_period_max=($anual_period_max > $course_subject_student->countCourseSubjectStudentPeriods())?$anual_period_max:$course_subject_student->countCourseSubjectStudentPeriods();
          break;
        case 2:
          $course_subject_per_type['QUATERLY'][] = $course_subject_student;
          $course_subject_per_type['QUATERLY']['marks'] = max($quaterly_max, $course_subject_student->countCourseSubjectStudentMarks());
          $course_subject_per_type['QUATERLY']['periods'] = max($quaterly_period_max, $course_subject_student->countCourseSubjectStudentPeriods());
          $quaterly_max       =($quaterly_max > $course_subject_student->countCourseSubjectStudentMarks())?$quaterly_max:$course_subject_student->countCourseSubjectStudentMarks();
          $quaterly_period_max=($quaterly_period_max > $course_subject_student->countCourseSubjectStudentPeriods())?$quaterly_period_max:$course_subject_student->countCourseSubjectStudentPeriods();
          break;
        case 3:
          $course_subject_per_type['BIMESTER'][] = $course_subject_student;
          $course_subject_per_type['BIMESTER']['marks'] = max($bimester_max, $course_subject_student->countCourseSubjectStudentMarks());
          $course_subject_per_type['BIMESTER']['periods'] = max($bimester_period_max, $course_subject_student->countCourseSubjectStudentPeriods());
          $bimester_max       =($bimester_max > $course_subject_student->countCourseSubjectStudentMarks())?$bimester_max:$course_subject_student->countCourseSubjectStudentMarks();
          $bimester_period_max=($bimester_period_max > $course_subject_student->countCourseSubjectStudentPeriods())?$bimester_period_max:$course_subject_student->countCourseSubjectStudentPeriods();
          break;
        case 4:
          $course_subject_per_type['QUATERLY_OF_A_TERM'][] = $course_subject_student;
          $course_subject_per_type['QUATERLY_OF_A_TERM']['marks'] = max($quaterly_of_a_term_max, $course_subject_student->countCourseSubjectStudentMarks());
          $course_subject_per_type['QUATERLY_OF_A_TERM']['periods'] = max($quaterly_of_a_term_period_max, $course_subject_student->countCourseSubjectStudentPeriods());
          $quaterly_of_a_term_max =($quaterly_of_a_term_max > $course_subject_student->countCourseSubjectStudentMarks())?$bimester_max:$course_subject_student->countCourseSubjectStudentMarks();
          $quaterly_of_a_term_period_max=($quaterly_of_a_term_period_max > $course_subject_student->countCourseSubjectStudentPeriods())?$quaterly_of_a_term_period_max:$course_subject_student->countCourseSubjectStudentPeriods();
          break;
      }
    }
    return $course_subject_per_type;
  }

  /**
   * Returns the status as string.
   *
   * @return string
   */
  public function getStatusString()
  {
    $css = CareerStudentStatus::getInstance("StudentCareerSchoolYearStatus");

    return $css->getStringFor($this->getStatus());
  }

  public function isInCourse()
  {
    return $this->getStatus() == StudentCareerSchoolYearStatus::IN_COURSE;
  }

  public function isApproved()
  {
    return $this->getStatus() == StudentCareerSchoolYearStatus::APPROVED;
  }

  public function isRepproved()
  {
    return $this->getStatus() == StudentCareerSchoolYearStatus::REPPROVED;
  }

  public function isWithdraw()
  {
    return $this->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN;
  }

  public function isWithdrawWithReserve()
  {
    return $this->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE;
  }
  
  public function suggestYear()
  {
    return $this->isApproved() ? $this->getYear() + 1 : $this->getYear();
  }

  /**
   * Returns the anual average.
   *
   * @return float
   */
  public function getAnualAverage()
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getAnualAverageForStudentCareerSchoolYear($this);
  }


  /**
   * Returns avg of marks for the quaterly given in the scope of considered course_types.
   * It also considers those course_subjects with only one mark as sometimes they don't need to be averaged
   *
   * @return float
   */
  public function getAvgFor($quaterly_number, $course_types_array)
  {
    $course_subject_students = SchoolBehaviourFactory::getInstance()->getCourseSubjectStudentsForCourseType($this->getStudent(), $course_types_array, $this->getSchoolYear());
    $sum = 0;
    $count = 0;

    foreach ($course_subject_students as $course_subject_student)
    {
      if ($course_subject_student->getCourseSubject()->getCourseType() != CourseType::QUATERLY)
      {
        $configs = $course_subject_student->getCourseSubject()->getCourseSubjectConfigurations();
        $config = array_shift($configs);

        //estas especificaciones son necesarias ya que hay materias cuatrimestrales con una sola nota y por ende según en qué cuatrimestre se cursen su nota debe o no ser promediada
        if ($quaterly_number == 1)
        {
          if (($config->getPeriod()->isBimester() && $config->parentIsFirst()) || ($course_subject_student->getCourseSubject()->getCourseType() == CourseType::QUATERLY_OF_A_TERM && $config->isForFirstQuaterly()))
          {
            $course_subject_student_mark = $course_subject_student->getMarkFor($quaterly_number);
            if (!is_null($course_subject_student_mark))
            {
              $count++;
              if (!$course_subject_student_mark->getIsClosed())
              {
                return '';
              }

              $sum = $course_subject_student_mark->getMark() + $sum;
            }
          }
        }

        if ($quaterly_number == 2)
        {
          if (($config->getPeriod()->isBimester() && !$config->parentIsFirst()) || ($course_subject_student->getCourseSubject()->getCourseType() == CourseType::QUATERLY_OF_A_TERM && !$config->isForFirstQuaterly()))
          {
            $course_subject_student_mark = $course_subject_student->getMarkFor($quaterly_number - 1);

            if (!is_null($course_subject_student_mark))
            {
              $count++;
              if (!$course_subject_student_mark->getIsClosed())
              {
                return '';
              }

              $sum = $course_subject_student_mark->getMark() + $sum;
            }
          }
        }
      }
      else
      {
        $course_subject_student_mark = $course_subject_student->getMarkFor($quaterly_number);
        if (!is_null($course_subject_student_mark))
        {
          $count++;
          if (!$course_subject_student_mark->getIsClosed())
          {
            return '';
          }

          $sum = $course_subject_student_mark->getMark() + $sum;
        }
      }
    }

    $avg = $sum / $count;
    $avg = sprintf('%.4s', $avg);

    return $avg;
  }

  public function setFree()
  {
    $this->setIsFree(true);
    $this->save();
  }

  public function getIsRepproved()
  {
    return $this->getStatus() == StudentCareerSchoolYearStatus::REPPROVED || $this->getStatus() == StudentCareerSchoolYearStatus::LAST_YEAR_REPPROVED;
  }

  public function isLastYear()
  {
    return $this->getYear() == $this->getCareerSchoolYear()->getCareer()->getQuantityYears();
  }

  public function getSchoolYear()
  {
    return $this->getCareerSchoolYear()->getSchoolYear();
  }

  public function isAbsenceForPeriod()
  {
    return $this->getCareerSchoolYear()->getIsAbsenceForPeriodInYear($this->getYear());
  }

  public function getMaxAbsenceForPeriod($period)
  {
    return $this->getCareerSchoolYear()->getMaxAbsenceForPeriod($period,$this->getYear());
  }

  public function getDivisions()
  {
    $c = new Criteria();
    $c->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $this->getCareerSchoolYearId());
    $c->addJoin(DivisionPeer::ID, DivisionStudentPeer::DIVISION_ID);
    $c->add(DivisionStudentPeer::STUDENT_ID, $this->getStudentId());

    return DivisionPeer::doSelect($c);
  }


}
