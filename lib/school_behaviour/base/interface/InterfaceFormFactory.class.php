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
 * Description of InterfaceFormFactory
 *
 * @author gramirez
 */
abstract class InterfaceFormFactory
{

/**
   * Returns form used by teacher/new|edit actions
   *
   * @see teacherActions
   * @return string represents a Form PHPClass
   */
  public abstract function getTeacherForm();

  /**
   * Returns form used by personal/new|edit actions
   *
   * @see personalActions
   * @return string represents a Form PHPClass
   */
  public abstract  function getPersonalForm();

  /**
   * Returns form used by student/registerForCareer action
   *
   * @see studentActions::executeRegisterForCareer
   * @return string represents a Form PHPClass
   */
  public abstract function getRegisterStudentForCareerForm();

  /**
   * Returns form used by course/new action
   *
   * @see courseGeneratorConfiguration::getForm
   * @return string represents a Form PHPClass
   */
  public abstract function getCourseForm();

  /**
   * Returns form used by student new|edit action
   *
   * @return string represents a Form PHPClass
   */
  public abstract function getStudentForm();

  /**
   * Returns form used by career_school_year for configuration.
   *
   * @return string represents a Form PHPClass
   */
  public abstract function getCareerSchoolYearConfigurationForm();

  /**
   * Returns form used by career_subject_school_year for configuration.
   *
   * @return string represents a Form PHPClass
   */
  public abstract function getCareerSubjectSchoolYearConfigurationForm();

  /**
   * Returns form used by course_subject_student for marks.
   *
   * @return stringg represents a Form PHPClass
   */
  public abstract function getCourseSubjectMarksForm();
  
  /**
   * Returns the form used by career_subject_school_year for multiple subject configuration.
   *
   * @return string A string that represents a Form
   */
  public abstract function getMultipleSubjectConfigurationForm();

  /**
   * Returns the form used by course subject students for edit the course students regularity in a course
   *
   * @return string A string that represents a Form
   */
  public abstract function getCourseSubjectStudentsRegularityForm();

  /**
   * Returns the form used by course subject students for edit the student regularity in its courses
   *
   * @return string A string that represents a Form
   */
  public abstract function getStudentCoursesRegularityForm();
  /**
   * Returns form filter used by division_course.
   *
   * @return string represents a Form PHPClass
   */
  abstract public function getDivisionCourseFormFilter();
}