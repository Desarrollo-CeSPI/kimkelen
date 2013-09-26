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
class StudentApprovedCareerSubjectBehavior
{
  /**
   * Check if a student has approved all career_subjects of the career, then graduates the student.
   *
   * @param StudentApprovedCareerSubject $student_approved_career_subject
   * @param PropelPDO $con
   */
  public function graduateStudent(StudentApprovedCareerSubject $student_approved_career_subject, PropelPDO $con = null)
  {


    //habria que chequear que este en el ultimo año del plan, que tenga todas aprobadas las materias del ultimo año y que no tenga previas.
  $student = $student_approved_career_subject->getStudent();
  $career = $student_approved_career_subject->getCareerSubject()->getCareer();
$school_year = $student_approved_career_subject->getSchoolYear();
  $last_year_subjects = $career->getMaxYearSubject();

  //$approved_career_subjects = StudentApprovedCareerSubjectPeer::getByStudentAndSchoolYear($student, $school_year);
  return;

  }
}