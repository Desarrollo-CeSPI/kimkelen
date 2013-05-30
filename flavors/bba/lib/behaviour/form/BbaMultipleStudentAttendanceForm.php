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
class BbaMultipleStudentAttendanceForm extends MultipleStudentAttendanceForm
{

  public function setAbsenceWidget($name)
  {
    if ($this->isAttendanceBySubject())
    {
      $widget = new sfWidgetFormInput();
      $validator = new sfValidatorNumber(array('required' => false));
    }
    else
    {
      $widget = new sfWidgetFormPropelChoice(array('model' => 'AbsenceType'));
      $validator = new sfValidatorPropelChoice(array('model' => 'AbsenceType', 'required' => false));
    }

    $this->setWidget($name, $widget);
    $this->setValidator($name, $validator);

  }

  public function getAttendanceValue($name)
  {
    if ($this->isAttendanceBySubject())
    {
      return $this->getValue($name);
    }
    else
    {
      return AbsenceTypePeer::retrieveByPK($this->getValue($name))->getValue();
    }

  }
  public function setAttendanceDefault($name, $student_attendance)
  {
    if ($this->isAttendanceBySubject())
    {
      $this->getWidget($name)->setDefault($student_attendance->getValue());
    }
    else
    {
      $this->getWidget($name)->setDefault($student_attendance->getAbsenceTypeId());
    }
  }

  public function setStudentAttendanceValue($name, $student_attendance)
  {
    $value = $this->getAttendanceValue($name);
    $student_attendance->setValue($value);
    if (!$this->isAttendanceBySubject())
    {
      $student_attendance->setAbsenceTypeId($this->getValue($name));
    }
  }
}