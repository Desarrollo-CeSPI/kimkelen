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
class MultipleStudentAttendanceForm extends sfForm
{

  public function configure()
  {
    parent::configure();

    $this->widgetSchema->setNameFormat('multiple_student_attendance[%s]');
    $this->validatorSchema->setOption('allow_extra_fields', true);

    $this->setWidget('year', new sfWidgetFormInputHidden());
    $this->setValidator('year', new sfValidatorPass());

    $this->setWidget('day', new sfWidgetFormInputHidden());
    $this->setValidator('day', new sfValidatorPass());

    $this->setWidget('career_school_year_id', new sfWidgetFormInputHidden());
    $this->setValidator('career_school_year_id', new sfValidatorPass());

    $this->setWidget('course_subject_id', new sfWidgetFormInputHidden());
    $this->setValidator('course_subject_id', new sfValidatorPass());

    $this->setWidget('division_id', new sfWidgetFormInputHidden());
    $this->setValidator('division_id', new sfValidatorPass());

    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'validarPeriodo'))));

  }

  public function validarPeriodo($validator, $values)
  {
    if ($this->isAttendanceBySubject())
    {
      $course_subject = CourseSubjectPeer::retrieveByPK($values['course_subject_id']);
      if (!$course_subject->getCareerSchoolYearPeriods()){
        $error = new sfValidatorError($validator, 'El curso no posee periodos');
        throw new sfValidatorErrorSchema($validator, array('course_subject_id' => $error));
    }

      if (!$course_subject->isInPeriod($values['day']))
      {

        $error = new sfValidatorError($validator, 'La falta ingresada no pertenece a un periodo valido para el curso');
        throw new sfValidatorErrorSchema($validator, array('course_subject_id' => $error));
      }
    }
    return $values;

  }

  private function getStudents()
  {
    if ($this->isAttendanceBySubject())
    {
      return $this->getCourseSubject()->getStudents();
    }
    else
    {
      return $this->getDivision()->getStudents();
    }
  }

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

    //$this->students = StudentPeer::getStudentsForAttendance($sf_user, $this->division_id, $this->course_subject_id, $this->career_school_year_id);
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

  public function setAttendanceDefault($name, $student_attendance)
  {
    $this->getWidget($name)->setDefault($student_attendance->getAbsenceTypeId());
  }

  public function configureDaysWidget($days_disabled = null)
  {
    $today = strtotime(date('Y-m-d'));
    foreach ($this->days as $day => $day_i)
    {
      $name = 'day_disabled_' . $day;
      $this->setWidget($name, new sfWidgetFormInputCheckbox());
      $this->setValidator($name, new sfValidatorBoolean(array('required' => false)));

      $this->getWidget($name)->setAttribute('onClick', "disableColumn($day)");
      //$this->setDefault($name, $days_disabled[$day]);
      if ($this->isAttendanceBySubject())
      {
        $this->setDefault($name, !$this->getCourseSubject()->isConfiguredToCourse($day) && !$this->getCourseSubject()->hasAttendanceForDate($day_i));
      }
    }
  }

  public function getCourseSubject()
  {
    return $this->course_subject;
  }

  public function getDivision()
  {
    return $this->division;
  }

  public function getNextDivision()
  {
    $sf_user = sfContext::getInstance()->getUser();
    return DivisionPeer::retrieveNextDivisionFor($this->division, $sf_user);    
  }

  public function getPreviousDivision()
  {
    $sf_user = sfContext::getInstance()->getUser();
    return DivisionPeer::retrievePreviousDivisionFor($this->division, $sf_user);    
  }

  public function configureDays()
  {
    $this->day = $this->getDefault('day');
    $this->day = str_replace('/', '-', $this->day);

    $this->day = date('Y-m-d', strtotime($this->day));
    $day_of_week = date("w", strtotime($this->day));

    for ($i = 1; $i < $day_of_week; $i++)
    {
      $diff = $day_of_week - $i;
      $this->days[$i] = date('Y-m-d', strtotime($this->day . '- ' . $diff . 'days'));
    }

    $this->days[$day_of_week] = $this->day;

    for ($i = $day_of_week + 1; $i < 6; $i++)
    {
      $diff = $i - $day_of_week;
      $this->days[$i] = date('Y-m-d', strtotime($this->day . '+ ' . $diff . 'days'));
    }

  }

  public function setAbsenceWidget($name)
  {
    $c = new Criteria();
//    $c->addAscendingOrderByColumn(AbsenceTypePeer::VALUE);
    $c->addAscendingOrderByColumn(AbsenceTypePeer::ORDER);
    if ($this->isAttendanceBySubject())
    {
      $c->add(AbsenceTypePeer::METHOD, AbsenceMethod::SUBJECT);
      $widget = new sfWidgetFormPropelChoice(array('model' => 'AbsenceType', 'criteria' => $c));
      $validator = new sfValidatorPropelChoice(array('model' => 'AbsenceType', 'required' => false, 'criteria' => $c));
    }
    else
    {
      $c->add(AbsenceTypePeer::METHOD, AbsenceMethod::DAY);
      $widget = new sfWidgetFormPropelChoice(array('model' => 'AbsenceType', 'criteria' => $c));
      $validator = new sfValidatorPropelChoice(array('model' => 'AbsenceType', 'required' => false, 'criteria' => $c));
    }

    $this->setWidget($name, $widget);
    $this->setWidget($name, $widget);
    $this->setValidator($name, $validator);

  }

  public function save()
  {
    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();

      foreach ($this->students as $student)
      {
        foreach ($this->days as $day => $day_i)
        {
          $student_attendance = StudentAttendancePeer::retrieveOrCreate($student, $this->course_subject_id, $day_i, $this->career_school_year_id);

          if (!$this->getValue('day_disabled_' . $day))
          {
            $name = 'student_attendance_' . $student->getId() . '_' . $day;

            $this->setStudentAttendanceValue($name, $student_attendance);
            $student_attendance->save($con);
          }
          elseif (!$student_attendance->isNew())
          {
            $student_attendance->delete($con);
          }
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

  public function isAttendanceBySubject()
  {
    return $this->course_subject_id !== null;
  }

  public function getAttendanceValue($name)
  {
    if ($this->isAttendanceBySubject())
    {
      return AbsenceTypePeer::retrieveByPK($this->getValue($name))->getValue();
    }
    else
    {
      return AbsenceTypePeer::retrieveByPK($this->getValue($name))->getValue();
    }
  }

  public function setStudentAttendanceValue($name, $student_attendance)
  {
    $value = $this->getAttendanceValue($name);
    $student_attendance->setValue($value);
    $student_attendance->setAbsenceTypeId($this->getValue($name));
  }

  public function getCareerSchoolYearPeriods()
  {
    if ($this->isAttendanceBySubject())
    {
      return $this->getCourseSubject()->getCareerSchoolYearPeriods();
    }
    else
    {
      return $this->getDivision()->getCareerSchoolYearPeriods();
    }
  }

  public function isAbsenceForPeriod()
  {
    if ($this->isAttendanceBySubject())
    {
      return $this->getCourseSubject()->getIsAbsenceForPeriod();
    }
    else
    {
      return $this->getDivision()->getIsAbsenceForPeriod();
    }
  }

  public function getCareerSchoolYear()
  {
    if ($this->isAttendanceBySubject())
    {
      return $this->getCourseSubject()->getCareerSchoolYear();
    }
    else
    {
      return $this->getDivision()->getCareerSchoolYear();
    }
  }

  public function getJavascripts()
  {
    return array_merge(parent::getJavascripts(), array('student_attendance.js'));
  }
}