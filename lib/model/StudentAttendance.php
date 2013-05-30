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

class StudentAttendance extends BaseStudentAttendance
{
  public function  __toString()
  {
    return $this->getStudent() . ' | ' . $this->getDay() . ' | ' . $this->getValueString();
  }

  public function getValueString()
  {
    return ($this->getAbsenceType())? $this->getAbsenceType()->getName(): $this->getValue();
  }

  public function getFormattedDay()
  {
    return date('d-m-Y',strtotime($this->getDay()));
  }


  public function  getStudentAttendanceJustificationOrCreate(PropelPDO $con = null)
  {
    if (is_null($this->getStudentAttendanceJustification()))
    {
      return new StudentAttendanceJustification($con);
    }

    return $this->getStudentAttendanceJustification($con);
  }

  public function renderChangeLog()
  {
    return ncChangelogRenderer::render($this, 'tooltip', array('credentials' => 'view_changelog'));
  }

  public function hasJustification()
  {
    return !is_null($this->getStudentAttendanceJustification());
  }
}

sfPropelBehavior::add('StudentAttendance', array('changelog'));