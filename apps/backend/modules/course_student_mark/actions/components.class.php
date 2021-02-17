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
 * @author ncuesta
 */
class course_student_markComponents extends sfComponents
{
  public function executeMarks()
  {

    $this->course         = $this->getVar('course');
    $this->course_subject = $this->getVar('course_subject');

    $this->configuration  = $this->course_subject->getCareerSubjectSchoolYear()->getConfiguration();

	  if (!$this->course->getIsPathway()) {
      $this->course_subject_students = $this->course_subject->getCourseSubjectStudents();
	  }else {
		  $this->course_subject_students = $this->course_subject->getCourseSubjectStudentPathways();
	  }
  }

  public function executeMark()
  {
    $this->course_subject = $this->getVar('course_subject');

    $this->course_subject_student = $this->getVar('course_subject_student');

    $this->marks = $this->course_subject_student->getAvailableCourseSubjectStudentMarks();
  }

	public function executePathwayMark()
	{
		$this->course_subject = $this->getVar('course_subject');

		$this->course_subject_student = $this->getVar('course_subject_student');
	}

  public function executeComponent_close()
  {
    $this->course_subject = $this->getVar('course_subject');

    $this->course_subject_student = $this->getVar('course_subject_student');

    $this->marks = $this->course_subject_student->getCourseSubjectStudentMarks();
  }

  public function executeComponent_marks_info()
  {
    $this->course_subject_student = $this->getVar('course_subject_student');

    $this->marks = $this->course_subject_student->getAvailableCourseSubjectStudentMarks();

    $c = new Criteria();
    $c->add(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, $this->course_subject_student->getId());
    $criterion = $c->getNewCriterion(CourseSubjectStudentExaminationPeer::MARK, null, Criteria::ISNOTNULL);
    $criterion->addOr($c->getNewCriterion(CourseSubjectStudentExaminationPeer::IS_ABSENT, true));

    $c->add($criterion);
    $c->addAscendingOrderByColumn(CourseSubjectStudentExaminationPeer::EXAMINATION_NUMBER);

    $this->course_subject_student_examinations = CourseSubjectStudentExaminationPeer::doSelect($c);

    $c = new Criteria();
    $c->add(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, $this->course_subject_student->getId(). Criteria::INNER_JOIN);
    $c->addJoin(StudentExaminationRepprovedSubjectPeer::STUDENT_REPPROVED_COURSE_SUBJECT_ID, StudentRepprovedCourseSubjectPeer::ID, Criteria::INNER_JOIN);
    $c->addJoin(StudentExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_SUBJECT_ID, ExaminationRepprovedSubjectPeer::ID, Criteria::INNER_JOIN);
    $c->addJoin(ExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_ID, ExaminationRepprovedPeer::ID, Criteria::INNER_JOIN);
    $c->addAscendingOrderByColumn(ExaminationRepprovedPeer::EXAMINATION_NUMBER);

    $criterion = $c->getNewCriterion(StudentExaminationRepprovedSubjectPeer::MARK, null, Criteria::ISNOTNULL);
    $criterion->addOr($c->getNewCriterion(StudentExaminationRepprovedSubjectPeer::IS_ABSENT, true));

    $c->add($criterion);

    $this->student_examination_repproved_subjects = StudentExaminationRepprovedSubjectPeer::doSelect($c);

	  //$this->pathway_mark = $this->course_subject_student->getCourseSubjectStudentPathwayMark()->getMark();
  }
  
  
  public function executeMarks_not_averageable()
  {

    $this->course         = $this->getVar('course');
    $this->course_subject = $this->getVar('course_subject');

    $this->configuration  = $this->course_subject->getCareerSubjectSchoolYear()->getConfiguration();

	  if (!$this->course->getIsPathway()) {
      $this->course_subject_students = $this->course_subject->getCourseSubjectStudents();
	  }else {
		  $this->course_subject_students = $this->course_subject->getCourseSubjectStudentPathways();
	  }
  }
  
  
  public function executeMark_not_averageable()
  {
    $this->course_subject = $this->getVar('course_subject');

    $this->course_subject_student = $this->getVar('course_subject_student');
  }

}