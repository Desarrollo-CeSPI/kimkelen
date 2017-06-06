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
 * Description of BaseFormFactory
 *
 * @author gramirez
 */
abstract class BaseFormFactory extends InterfaceFormFactory{

  /**
   * Returns form used by tutor/new|edit actions
   *
   * @see tutorActions
   * @return string represents a Form PHPClass
   */
  public function getTutorForm()
  {
    return 'TutorForm';
  }


  /**
   * Returns form used by teacher/new|edit actions
   *
   * @see teacherActions
   * @return string represents a Form PHPClass
   */
  public function getTeacherForm()
  {
    return 'TeacherForm';
  }

  /**
   * Returns form used by personal/new|edit actions
   *
   * @see personalActions
   * @return string represents a Form PHPClass
   */
  public function getPersonalForm()
  {
    return 'PersonalForm';
  }

  /**
   * Returns form used by student/registerForCareer action
   *
   * @see studentActions::executeRegisterForCareer
   * @return string represents a Form PHPClass
   */
  public function getRegisterStudentForCareerForm()
  {
    return 'CareerStudentForm';
  }

  /**
   * Returns form used by course/new action
   *
   * @see courseGeneratorConfiguration::getForm
   * @return string represents a Form PHPClass
   */
  public function getCourseForm()
  {
    return 'DivisionCourseForm';
  }

  /**
   * Returns form used by student new|edit action
   *
   * @return string represents a Form PHPClass
   */
  public function getStudentForm()
  {
    return 'StudentForm';
  }

  /**
   * Returns form used to filter students in list action
   *
   * @return string represents a Form PHPClass
   */
  public function getStudentFormFilter()
  {
    return 'StudentFormFilter';
  }

  /**
   * Returns form used by career_school_year for configuration.
   *
   * @return string represents a Form PHPClass
   */
  public function getCareerSchoolYearConfigurationForm()
  {
    return 'SubjectConfigurationForm';
  }

  /**
   * Returns form used by career_subject_school_year for configuration.
   *
   * @return string represents a Form PHPClass
   */
  public function getCareerSubjectSchoolYearConfigurationForm()
  {
    return 'CareerSubjectConfigurationForm';
  }

  /**
   * Returns form used by course_subject_student for marks.
   *
   * @return stringg represents a Form PHPClass
   */
  public function getCourseSubjectMarksForm()
  {
    return 'CourseSubjectMarksForm';
  }

	/**
	 * Returns form used by course_subject_student for pathway marks.
	 *
	 * @return string represents a Form PHPClass
	 */
	public function getCourseSubjectPathwayMarksForm()
	{
		return 'CourseSubjectPathwayMarksForm';
	}

  /**
   * Returns form used by multiple registration of students.
   *
   * @return string represents a Form PHPClass
   */
  public function getMultipleCareerRegistrationForm()
  {
    return "MultipleCareerRegistrationForm";
  }
  /**
   * Returns form used by multiple configuration of career subjects.
   *
   * @return string represents a Form PHPClass
   */
  public function getMultipleSubjectConfigurationForm()
  {
    return "MultipleSubjectConfigurationForm";
  }


  public function getCourseSubjectStudentsRegularityForm()
  {
    return 'CourseSubjectStudentsRegularityForm';
  }

  public function getStudentCoursesRegularityForm()
  {
    return 'StudentCoursesRegularityForm';
  }

  /**
   * Returns form used by commission/new action
   *
   * @see commissionGeneratorConfiguration::getForm
   * @return string represents a Form PHPClass
   */
  public function getCommissionForm()
  {
    return 'CommissionForm';
  }

  /**
   * Returns form filter used by division_course.
   *
   * @return string represents a Form PHPClass
   */
  public function getDivisionCourseFormFilter()
  {
    return 'DivisionCourseFormFilter';
  }

  /**
   * Returns form filter used by attendance justification
   *
   * @return string represent a Form PHPClass
   */
  public function getAttendanceJustificationFormFilter()
  {
    return 'AttendanceJustificationFormFilter';
  }

  /**
   * Returns the form used in the division creation
   *
   * @return string represent a Form PHPClass
   */
  public function getDivisionForm()
  {
    return 'DivisionForm';
  }

  /**
   * Returns the form used in the classroom creation
   *
   * @return string represent a Form PHPClass
   */
  public function getClassroomForm()
  {
    return 'ClassroomForm';
  }

  /**
   * Returns the form used in the head_personal creation
   *
   * @return string represent a Form PHPClass
   */
  public function getHeadPersonalForm()
  {
    return 'HeadPersonalForm';
  }

  /**
   * Returns the form used in the student_office creation
   *
   * @return string represent a Form PHPClass
   */
  public function getStudentOfficePersonalForm()
  {
    return 'StudentOfficePersonalForm';
  }

  /**
   * Returns the form used in the student_discilinary_sanction creation
   *
   * @return string represent a Form PHPClass
   */
  public function getStudentDisciplinarySanctionForm()
  {
    return 'StudentDisciplinarySanctionForm';
  }

  public function getMultipleStudentAttendanceForm()
  {
    return 'MultipleStudentAttendanceForm';
  }

  /**
   * Returns form used by student examiantion repproved subject new|edit action
   *
   * @return string represents a Form PHPClass
   */
  public function getStudentExaminationRepprovedSubjectForm()
  {
    return 'StudentExaminationRepprovedSubjectForm';
  }
  /**
   * Returns form used by a pathwat commission/new action
   *
   * @see pathwayCommissionGeneratorConfiguration::getFormClass
   * @return string represents a Form PHPClass
   */
  public function getPathwayCommissionForm()
  {
    return 'PathwayCommissionForm';
  }
  
  public function getMultipleStudentAttendanceDayForm()
  {
    return 'MultipleStudentAttendanceDayForm';
  }
  
  public function getStudentDisciplinarySanctionFormFilter()
  {
    return 'StudentDisciplinarySanctionFormFilter';
  }
  
  public function getTutorFormFilter()
  {
    return 'TutorFormFilter';
  }
  
  public function getMultipleStudentAttendancePathwayForm()
  {
      return 'MultipleStudentAttendancePathwayForm';
  }
  
}
