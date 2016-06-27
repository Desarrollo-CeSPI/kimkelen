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
    
    foreach ($this->students as $student)
    {
      $name = 'student_name_' . $student->getId();
      $this->setWidget($name, new mtWidgetFormPlain(array('object' => $student)));
      $this->setValidator($name, new sfValidatorPass());

      
      $name = 'student_attendance_' . $student->getId() . '_' . $this->day;
      $this->setAbsenceWidget($name);
      $student_attendance = StudentAttendancePeer::retrieveByDateAndStudent($this->day, $student, $this->course_subject_id, $this->career_school_year_id);

      if (!is_null($student_attendance))
      {
        $this->setAttendanceDefault($name, $student_attendance);  
      }
      
    }
  }
  public function configureDays()
  {
    $this->day = $this->getDefault('day');
    $this->day = str_replace('/', '-', $this->day);
    $this->day = date('Y-m-d', strtotime($this->day));

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

          $name = 'student_attendance_' . $student->getId() . '_' . $this->day;
          $this->setStudentAttendanceValue($name, $student_attendance);
          $student_attendance->save($con);
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
