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

class BbaSchoolBehaviour extends BaseSchoolBehaviour
{

  const school_name = "Bachillerato de Bellas Artes";

  protected
  $_course_type_options = array(
    CourseType::TRIMESTER => 'Anual con Régimen Trimestral',
    CourseType::QUATERLY => 'Anual con Régimen Cuatrimestral',
    CourseType::BIMESTER => 'Cuatrimestral con Régimen Bimestral'
  );

  const OLD_CAREER = 4;

  public function getListObjectActionsForSchoolYear()
  {
    return array(
      'change_state' => array('action' => 'changeState', 'condition' => 'canChangedState', 'label' => 'Cambiar vigencia', 'credentials' => array(0 => 'edit_school_year',),),
      'registered_students' => array('action' => 'registeredStudents', 'credentials' => array(0 => 'show_school_year',), 'label' => 'Registered students',),
      'careers' => array('action' => 'schoolYearCareers', 'label' => 'Ver carreras', 'credentials' => array(0 => 'show_career',),),
      'examinations' => array('action' => 'examinations', 'label' => 'Examinations', 'credentials' => array(0 => 'show_examination',), 'condition' => 'canExamination',),
      'examination_repproved' => array('action' => 'examinationRepproved', 'label' => 'Examination repproved', 'credentials' => array(0 => 'show_examination_repproved',), 'condition' => 'canExamination',),
      '_delete' => array('credentials' => array(0 => 'edit_school_year',), 'condition' => 'canBeDeleted',),
    );

  }

  /**
   * Return the title for the exportation in shared_course_subject
   *
   * @param Teacher $teacher
   * @return string
   */
  public function getExportationSharedCourseSubjectTitle(Teacher $teacher)
  {
    return 'Asignaturas de ' . $teacher;

  }

  /**
   * Returns the valid hours for a course
   *
   * @return array
   */
  public function getHoursArrayForSubjectWeekday()
  {
    return array(6 => "06", 7 => "07", 8 => "08", 9 => "09", 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22);

  }

  /**
   * Returns the valid hours for a course
   *
   * @return array
   */
  public function getMinutesArrayForSubjectWeekday()
  {
    return array(0 => "00", 5 => "05", 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40, 45 => 45, 50 => 50, 55 => 55);

  }

  /**
   * This method all the preceptos can see all the students
   *
   * @param Criteria $criteria
   * @param integer $user_id
   */
  public function joinPreceptorWithStudents($criteria, $user_id)
  {
    return $criteria;

  }

  /*
   * This method redefines the parent, because in BBA school when a course is optional, all the students or any orientation can course it.
   *
   * @param CourseSubject $course_subject
   * @param Criteria $criteria
   * @param Boolean $filter_by_orientation
   *
   * @return Criteria $criteria
   */

  public function getAvailableStudentsForCourseSubjectCriteria(CourseSubject $course_subject, $criteria = null, $filter_by_orientation = true)
  {
    return parent::getAvailableStudentsForCourseSubjectCriteria($course_subject, $criteria, false);
  }

  /**
   * This method returns 'final_mark' if the course type is bimester/quaterly and the mark is 3
   *
   * @param type $mark
   * @param type $course_type
   *
   * return String dependending on the mark
   */
  public function getMarkTitle($mark, $course_type = null)
  {
    if ($course_type != CourseType::TRIMESTER && $mark == 3)
      return 'Final mark';
    else
      return 'Mark %number%';

  }

  public function getFreeLabel(CourseSubjectStudentMark $course_subject_student_mark)
  {
    if ($course_subject_student_mark->getCourseSubjectStudent()->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration()->getEvaluationMethod() == EvaluationMethod::FINAL_PROM)
    {
      return __('Absence:');
    }
    else
    {
      return __('Free:');
    }

  }

  public function getShortFreeLabel(CourseSubjectStudentMark $course_subject_student_mark)
  {
    if ($course_subject_student_mark->getCourseSubjectStudent()->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration()->getEvaluationMethod() == EvaluationMethod::FINAL_PROM && $course_subject_student_mark->getMarkNumber() == 3)
    {
      return 'A';
    }
    else
    {
      return 'L';
    }

  }

  public function getPrintReportUrlFor(Division $division)
  {
    if ($division->getYear() >= 1 && $division->getYear() < 4)
    {
      return 'Report::Kimkelen_BBA/boletin-trimestral-asistencia-por-año.prpt';
    }
    elseif ($division->getYear() >= 4)
    {
      #return 'Report::Kimkelen_BBA/boletin-anual-bimestal-asistencia-por-materia.prpt';
      return 'Report::Kimkelen_BBA/boletin-trimestral-asistencia-por-materia.prpt';
    }

    throw new InvalidArgumentException('Invalid division year for report card.');

  }

  public function getMarkNameByNumberAndCourseType($number, $course_type)
  {
    if (($number == 3) && ($course_type == CourseType::QUATERLY || $course_type == CourseType::BIMESTER))
    {
      return "Examen final";
    }
    else
    {
      return parent::getMarkNameByNumberAndCourseType($number, $course_type);
    }

  }

  public function getOrientationText(Career $career)
  {
    if (self::OLD_CAREER == $career->getId())
    {
      return 'Especialidad';
    }
    else
    {
      return 'Lenguaje';
    }

  }

  public function getSuborientationText(Career $career)
  {
    if (self::OLD_CAREER == $career->getId())
    {
      return 'Orientación';
    }
    else
    {
      return 'Especialidad';
    }

  }

  public function getMarksForCourseType($course_type)
  {
    return 3;

  }

  public function getFormattedAssistanceValue($student_attendance)
  {

    switch ($student_attendance->getValue())
    {
      case 0:
        return '·';
        break;

      case 1:
        return 'A';
        break;

      default:
        return $student_attendance->getValue() . 'A';
        break;
    }

  }
  
    public function showReportCardAdmonitionDetails()
  {
    return true;
  }

}