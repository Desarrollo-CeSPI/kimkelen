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

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MultipleStudentAttendanceForm
 *
 * @author ivan
 */
class MultipleStudentAttendancePathwayForm extends MultipleStudentAttendanceForm
{
  public function validarPeriodo($validator, $values)
  {
        $period = CareerSchoolYearPeriodPeer::retrieveCurrentFirstQuaterly();
     
        $initial = strtotime($period->getStartAt());
        $final = strtotime($period->getEndAt());
        $day = strtotime($values['day']);

        if ($day < $initial || $day > $final)
        {
            $error = new sfValidatorError($validator, 'La falta ingresada no pertenece a un periodo valido para el curso');
            throw new sfValidatorErrorSchema($validator, array('course_subject_id' => $error));
        }
    
    return $values;

  }

  protected function getStudents()
  {
    return CourseSubjectStudentPathwayPeer::retrieveStudentsByCourseSubject($this->getCourseSubject());
  }

  public function configureStudents()
  {
    $this->year = $this->getDefault('year'); 
    $this->division_id = $this->getDefault('division_id');
    $this->course_subject_id = $this->getDefault('course_subject_id') == '' || $this->getDefault('course_subject_id') === null ? null : $this->getDefault('course_subject_id');

    $this->division = DivisionPeer::retrieveByPK($this->division_id);
    $this->course_subject = CourseSubjectPeer::retrieveByPK($this->course_subject_id);
    
    $this->career_school_year_id = $this->getCareerSchoolYear()->getId(); 
    $this->configureDays();

    $sf_user = sfContext::getInstance()->getUser();

    $this->students = $this->getStudents();
    $days_disabled = array();
    foreach ($this->students as $student)
    {
      $name = 'student_name_' . $student->getId();
      $this->setWidget($name, new mtWidgetFormPlain(array('object' => $student)));
      $this->setValidator($name, new sfValidatorPass());

      foreach ($this->days as $day => $day_i)
      {
        if (!isset($days_disabled[$day]))
        {
          $days_disabled[$day] = true;
        }

        $name = 'student_attendance_' . $student->getId() . '_' . $day;

        $this->setAbsenceWidget($name);
        $student_attendance = StudentAttendancePeer::retrieveByDateAndStudent($day_i, $student, $this->course_subject_id, $this->career_school_year_id);

        if (!is_null($student_attendance))
        {
          $this->setAttendanceDefault($name, $student_attendance);
          $days_disabled[$day] = false;
        }
      }
    }

    $this->configureDaysWidget($days_disabled);
    return $days_disabled;

  }

  public function getCareerSchoolYearPeriods()
  {   
    return array(CareerSchoolYearPeriodPeer::retrieveCurrentFirstQuaterly());
    
  }

  public function isAbsenceForPeriod()
  {
    return true;
  }

  public function getCareerSchoolYear()
  {
      
    $school_year= SchoolYearPeer::retrieveCurrent();
    $career = $this->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer();
    
    return CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($career, $school_year);
    
  }
}
