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

class StudentAttendanceJustification extends BaseStudentAttendanceJustification
{

  public function __toString()
  {
    return $this->getObservation();
  }
  /**
   * Returns the directory for the persons photos
   * @return String
   */
  public static function getDocumentDirectory()
  {
    return sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'justification-documents';
  }

  /**
   * Returns the full path of the document
   * @return String
   */
  public function getDocumentFullPath()
  {
    return self::getDocumentDirectory().DIRECTORY_SEPARATOR.$this->getDocument();
  }

  /**
   * This method returns the student of the absence justificated
   *
   * @return Student
   */
  public function getStudent()
  {
    $student_attendances = $this->getStudentAttendances();

    return array_shift($student_attendances)->getStudent();
  }

  /**
   * Returns a string of the days jusficated.
   *
   * @return string;
   */
  public function getJustifiedDays()
  {
    $student_attendances = $this->getStudentAttendances();
    $days = array_shift($student_attendances)->getDay();
    foreach ($student_attendances as $student_attendance)
    {
      $days .= '; ' . $student_attendance->getDay();
    }

    return $days;
  }

  public function canDelete()
  {
      return true;
  }

  public function renderChangeLog()
  {
    return ncChangelogRenderer::render($this, 'tooltip', array('credentials' => 'view_changelog'));
  }
}

sfPropelBehavior::add('StudentAttendanceJustification', array('changelog'));