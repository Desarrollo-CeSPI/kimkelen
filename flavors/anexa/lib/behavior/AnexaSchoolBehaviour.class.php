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
 * Copy and rename this class if you want to extend and customize
 */
class AnexaSchoolBehaviour extends BaseSchoolBehaviour
{
	protected $school_name = "Escuela Graduada Joaquín V. González";
        protected $araucano_code = 3186;
        protected $letter = "G";


	public function getListObjectActionsForSchoolYear()
  {
    return array(
      'change_state' => array('action' => 'changeState', 'condition' => 'canChangedState',  'label' => 'Cambiar vigencia',  'credentials' =>   array( 0 => 'edit_school_year' ,), ),
      'registered_students' => array('action' => 'registeredStudents', 'credentials' => array( 0 => 'show_school_year', ), 'label' => 'Registered students',  ),
      'careers' => array( 'action' => 'schoolYearCareers', 'label' => 'Ver carreras', 'credentials' =>  array( 0 => 'show_career', ),),
      'examinations' => array( 'action' => 'examinations', 'label' => 'Examinations', 'credentials' =>  array( 0 => 'show_examination',),'condition' => 'canExamination',  ),
      'examination_repproved' => array('action' => 'examinationRepproved', 'label' => 'Examination repproved', 'credentials' => array(0 => 'show_examination_repproved', ),'condition' => 'canExamination', ),
      '_delete' => array('credentials' => array( 0 => 'edit_school_year',),'condition' => 'canBeDeleted',),
    );
  }

	public function getOptionalCourseSubjectStudents($student, $school_year = null)
	{

		if (is_null($school_year))
		{
			$school_year = SchoolYearPeer::retrieveCurrent();
		}

		$c = new Criteria();
		$c->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());
		$c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
		$c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
		$c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
		$c->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
		$c->add(CareerSubjectPeer::IS_OPTION, true);
		CareerSubjectSchoolYearPeer::sorted($c);

		$course_subject_students = $student->getCourseSubjectStudents($c);


		$results = array();
		foreach ($course_subject_students as $css)
		{
			$results[] = $css;
		}

		return $results;

	}
}
