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
 * BbaFormFactory determines which forms are to be used for custom school
 * behaviours
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class BbaFormFactory extends BaseFormFactory
{
  /**
   * Returns form used by career_school_year for configuration.
   *
   * @return string represents a Form PHPClass
   */
  public function getCareerSchoolYearConfigurationForm()
  {
    return 'BbaSubjectConfigurationForm';
  }

  /**
   * Returns form used by career_subject_school_year for configuration.
   *
   * @return string represents a Form PHPClass
   */
  public function getCareerSubjectSchoolYearConfigurationForm()
  {
    return 'BbaCareerSubjectSchoolYearConfigurationForm';
  }

  /**
   * Returns form used by course_subject_student for marks.
   *
   * @return stringg represents a Form PHPClass
   */
  public function getCourseSubjectMarksForm()
  {
    return 'BbaCourseSubjectMarksForm';
  }

  public function getMultipleSubjectConfigurationForm()
  {
    return "BbaMultipleSubjectConfigurationForm";
  }

  public function getMultipleStudentAttendanceForm()
  {
    return 'BbaMultipleStudentAttendanceForm';
  }

   /**
   * Returns form used by student new|edit action
   *
   * @return string represents a Form PHPClass
   */
  public function getStudentForm()
  {
    return 'BbaStudentForm';
  }
  
  public function getStudentFormFilter()
  {
    return 'BbaStudentFormFilter';
  }
  
  public function getTutorFormFilter()
  {
    return 'BbaTutorFormFilter';
  }

  public function getCourseSubjectNotAverageableMarksForm()
  {
    return 'BbaCourseSubjectNotAverageableMarksForm';
  }

}
