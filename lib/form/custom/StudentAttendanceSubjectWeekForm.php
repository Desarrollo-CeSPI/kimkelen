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
 * Description of StudentAttendanceWeekForm
 *
 * @author ivan
 */
class StudentAttendanceSubjectWeekForm extends sfForm
{

  public function configure()
  {
    $student_id = $this->getOption('student_id');
    $this->student = StudentPeer::retrieveByPK($student_id);
    $this->career_school_year_id = $this->getOption('career_school_year_id');
    $this->course_subject_id = $this->getOption('course_subject_id');
    $this->division_id = $this->getOption('division_id');


    $sf_formatter_attendance_week = new sfWidgetFormSchemaFormatterAttendanceWeek($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter("AttendanceWeek", $sf_formatter_attendance_week);
    $this->getWidgetSchema()->setFormFormatterName('AttendanceWeek');




    $day = $this->getOption('day');
    $this->widgetSchema->setNameFormat('attendance_' . $student_id . '][%s]');
    #student
    $this->setWidget("student_id", new sfWidgetFormInputHidden());
    $this->setValidator("student_id", new sfValidatorPropelChoice(array(
        "model" => "Student",
        "required" => true
      )));
    $this->setWidget("student", new mtWidgetFormPlain(array('object' => $this->student)));

    for ($i = 0; $i <= 6; $i++)
    {
      $day_i = date('Y-m-d', strtotime($day . '-' . $i . 'day a go'));

      $student_attendance = StudentAttendancePeer::retrieveOrCreateByDateAndStudent($day_i, $this->student, $this->getOption('course_subject_id'));

      if ($student_attendance)
      {
        $this->setDefault("value_" . $i, $student_attendance->getAbsenceTypeId());
      }
      $this->setWidget("value_" . $i, $this->getAttendanceWidget());
      $this->getWidget("value_" . $i, $this->getAttendanceWidget())->setAttribute('class', 'attendance_week_input');
      $this->setValidator("value_" . $i, $this->getAttendanceValidator());
    }

    $this->setDefault("student_id", $student_id);
    $this->disableCSRFProtection();

      $this->course_subject = CourseSubjectPeer::retrieveByPK($this->course_subject_id);
      $period = CareerSchoolYearPeriodPeer::retrieveByPK($this->course_subject->getCourse()->getCurrentPeriod());
      $this->setWidget('period', new mtWidgetFormPartial(array('module'=>'student_attendance', 'partial'=>'totalAbsences','form'=>$this, 'parameters'=>array('career_school_year_id'=> $this->career_school_year_id, 'period'=>$period,'course_subject_id'=> $this->course_subject_id,'student'=>$this->student))));
      $this->getWidgetSchema()->moveField('period',sfWidgetFormSchema::LAST);

  }

  public function getStudentId()
  {
    return $this->getOption('student_id');

  }

  public function getAttendanceWidget()
  {
    return new sfWidgetFormInput();

  }

  public function getAttendanceValidator()
  {
    return new sfValidatorNumber(array('min'=>0,'required'=>false));
  }


}

?>