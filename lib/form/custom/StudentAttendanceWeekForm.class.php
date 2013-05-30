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
class StudentAttendanceWeekForm extends sfForm
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

    $sf_user = sfContext::getInstance()->getUser();

    if ($sf_user->isPreceptor())
      {
        $limit = SchoolBehaviourFactory::getInstance()->getDaysForMultipleAttendanceForm();
      }
      else
      {
        $limit = BaseSchoolBehaviour::DAYS_FOR_MULTIPLE_ATTENDANCE_FORM;
      }



    for ($i = 0; $i <= $limit; $i++)
    {
      $day_i = date('Y-m-d', strtotime($day . '-' . $i . 'day ago'));

      $student_attendance = StudentAttendancePeer::retrieveOrCreateByDateAndStudent($day_i, $this->student, $this->getOption('course_subject_id'));
      if ($student_attendance)
      {
        $this->setDefault("value_" . $i, $student_attendance->getAbsenceTypeId());
      }
      $this->setWidget("value_" . $i, $this->getAttendanceWidget());
      #$this->getWidget('value_'.$i)->setLabel($day_i);
      $this->setValidator("value_" . $i, $this->getAttendanceValidator());
    }
    #sumarle 7 dias al dia antes de mandarlo a la funcion
//    $total_absenses = $this->student->getAmountStudentAttendanceUntilDay( strtotime($day . '+ 7 day a go'));
//    $this->setWidget("total", new mtWidgetFormPartial(array('module'=>'student_attendance','partial'=>'total_absens','form'=>$this,'parameters'=>array('total'=>$total_absenses )) ));

    $this->setDefault("student_id", $student_id);
    $this->disableCSRFProtection();



      $this->setWidget('period', new mtWidgetFormPartial(array('module'=>'student_attendance', 'partial'=>'totalAbsences','form'=>$this, 'parameters'=>array('career_school_year_id'=> $this->career_school_year_id, 'course_subject_id'=> null,'student'=>$this->student,'day'=>$day))));
      $this->getWidgetSchema()->moveField('period',sfWidgetFormSchema::LAST);

  }

  public function getStudentId()
  {
    return $this->getOption('student_id');

  }

  public function getAttendanceWidget()
  {
    return new sfWidgetFormPropelChoice(array('model' => 'AbsenceType'));
  }

  public function getAttendanceValidator()
  {
    return new sfValidatorPropelChoice(array('model' => 'AbsenceType', 'required' => true));
  }


}

?>