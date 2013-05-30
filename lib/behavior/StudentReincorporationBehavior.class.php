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

/**
 * Description of StudentApprovedCareerSubjectBehavior
 *
 * @author gramirez
 */
class StudentReincorporationBehavior
{
  /**
   * Updates depends of the case a StudentCareerSchoolYear or a CourseSubjectStudent, sets free at false and updates the remaining absences.
   *
   * @param StudentAttendance $student_attendance
   * @param PropelPDO $con
   */
  public function updateReincorporation(StudentReincorporation $student_reincorporation, PropelPDO $con = null)
  {
    $student_reincorporation->updateFree($con, false);    
  }

  /**
   * On delete of the reincorporation, updates the remaining absences of the object (StudentCareerSchoolYear or CourseSubjectStudent).
   *
   * @param StudentAttendance $student_attendance
   * @param PropelPDO $con
   */
  public function deleteReincorporation(StudentReincorporation $student_reincorporation, PropelPDO $con = null)
  {
    $student_reincorporation->updateFree($con, true);    
  }
}