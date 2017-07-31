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
class MultipleStudentAttendanceDayForm extends MultipleStudentAttendanceForm
{
	
  public function configureStudents()
  {
    $this->year = $this->getDefault('year');
    $this->division_id = $this->getDefault('division_id');
    $this->course_subject_id = $this->getDefault('course_subject_id') == '' || $this->getDefault('course_subject_id') === null ? null : $this->getDefault('course_subject_id');

    $this->division = DivisionPeer::retrieveByPK($this->division_id);
    $this->course_subject = CourseSubjectPeer::retrieveByPK($this->course_subject_id);

    $this->career_school_year_id = $this->getDefault('career_school_year_id');
    $this->configureDays();

    $sf_user = sfContext::getInstance()->getUser();
    $this->students = $this->getStudents();
    $days_disabled = array();
    
    foreach ($this->students as $student)
    {
      if (!isset($days_disabled[$day]))
      {
         $days_disabled[$day] = true;
      }
      $name = 'student_name_' . $student->getId();
      $this->setWidget($name, new mtWidgetFormPlain(array('object' => $student)));
      $this->setValidator($name, new sfValidatorPass());

      
      $name = 'student_attendance_' . $student->getId() . '_' . 1;
      $this->setAbsenceWidget($name);
      $student_attendance = StudentAttendancePeer::retrieveByDateAndStudent($this->day, $student, $this->course_subject_id, $this->career_school_year_id);

      if (!is_null($student_attendance))
      {
        $this->setAttendanceDefault($name, $student_attendance);  
      }
      
    }
    
    $this->configureDaysWidget($days_disabled);
    return $days_disabled;
  }
  public function configureDays()
  {
    $this->day = $this->getDefault('day');
    $this->day = str_replace('/', '-', $this->day);
    $this->day = date('Y-m-d', strtotime($this->day));

  }

  public function configureDaysWidget($days_disabled = null)
  {
    $today = strtotime(date('Y-m-d'));

    $name = 'day_disabled_1';
    $this->setWidget($name, new sfWidgetFormInputCheckbox());
    $this->setValidator($name, new sfValidatorBoolean(array('required' => false)));

    $this->getWidget($name)->setAttribute('onClick', "disableColumn(1)");
      //$this->setDefault($name, $days_disabled[$day]);
    if ($this->isAttendanceBySubject())
    {
       $this->setDefault($name, !$this->getCourseSubject()->isConfiguredToCourse(1) && !$this->getCourseSubject()->hasAttendanceForDate($this->day));
    }
    
  }
  public function save()
  {
    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();

      foreach ($this->students as $student)
      {
          $student_attendance = StudentAttendancePeer::retrieveOrCreate($student, $this->course_subject_id,  $this->day, $this->career_school_year_id);

          if (!$this->getValue('day_disabled_1'))
          {
            $name = 'student_attendance_' . $student->getId() . '_1';

            $this->setStudentAttendanceValue($name, $student_attendance);
            $student_attendance->save($con);
          }
          elseif (!$student_attendance->isNew())
          {
            $student_attendance->delete($con);
          }
      }

      $con->commit();
    }
    catch (PropelExeption $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

}
