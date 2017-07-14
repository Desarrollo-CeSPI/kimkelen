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

class LVMFixStudentApprovedCourseSubjects2011Task extends sfBaseTask
{
	protected function createContextInstance($application = 'backend', $enviroment = 'dev', $debug = true)
	{
		$configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);

		sfContext::createInstance($configuration);
		sfContext::switchTo($application);

		$this->context = sfContext::getInstance();
	}

	protected function configure()
	{
		// // add your own arguments here
		// $this->addArguments(array(
		//   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
		// ));

		$this->addOptions(array(
			new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
			new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
			new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
			// add your own options here
		));

		$this->namespace = 'fix';
		$this->name = 'LVMFixStudentApprovedCourseSubjects2011';
		$this->briefDescription = '';
		$this->detailedDescription = <<<EOF
The [LVMFixStudentApprovedCourseSubjects2011|INFO] task creates missing CourseSubjectStudentApproved for school year 2011.
Call it with:

  [php symfony LVMFixStudentApprovedCourseSubjects2011|INFO]
EOF;
	}

	protected function execute($arguments = array(), $options = array())
	{
		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
		$connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

		$this->createContextInstance();

		$ids = array();
		$student_approved_course_subjects = StudentApprovedCourseSubjectPeer::doSelect(new Criteria());
		foreach ($student_approved_course_subjects as $sacs) {
			$ids[]= $sacs->getId();
		}

		$c = new Criteria();

		$c->add(CourseSubjectStudentPeer::CREATED_AT, strtotime('-5 years ago'), Criteria::LESS_THAN);
		$c->add(CourseSubjectStudentPeer::STUDENT_APPROVED_COURSE_SUBJECT_ID, $ids, Criteria::NOT_IN);
		$course_subject_students = CourseSubjectStudentPeer::doSelect($c);
		$this->logSection('sdfds', count($course_subject_students));
		foreach ($course_subject_students as $css) {
			$student_approved_course_subject = StudentApprovedCourseSubjectPeer::retrieveForCourseSujectStudentAndSchoolYearId($css, $css->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear()->getId());

			if (is_null($student_approved_course_subject)) {
				$student_approved_course_subject = new StudentApprovedCourseSubject();
				$student_approved_course_subject->setCourseSubject($css->getCourseSubject());
				$student_approved_course_subject->setStudent($css->getStudent());
				$student_approved_course_subject->setSchoolYear($css->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear());

				$student_approved_course_subject->save($connection);

				// despues de guardar el course, hay que actualizar el link en el css
				$css->setStudentApprovedCourseSubjectId($student_approved_course_subject->getId());
				$css->save($connection);
			}
		}
	}
}