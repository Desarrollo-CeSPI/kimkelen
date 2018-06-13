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

class StudentRepprovedCourseSubject extends BaseStudentRepprovedCourseSubject
{

  public function __toString()
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getStudentRepprovedResultString($this);
  }

  public function getStudent()
  {
    return $this->getCourseSubjectStudent()->getStudent();
  }

  public function getLastStudentExaminationRepprovedSubject()
  {
  	$c = new Criteria();
    $c->addDescendingOrderByColumn(StudentExaminationRepprovedSubjectPeer::ID);
    $c->add(StudentExaminationRepprovedSubjectPeer::STUDENT_REPPROVED_COURSE_SUBJECT_ID, $this->getId());

    return StudentExaminationRepprovedSubjectPeer::doSelectOne($c);
  }

  public function getClass()
  {
    return 'reprobado';
  }

  public function getSubject()
  {
    return $this->getCourseSubjectStudent()->getCourseSubject()->getSubject();
  }

  public function getMarksStr()
  {
    $result = implode(', ',array_map(create_function('$sers', 'return $sers->getValueString();'), $this->getStudentExaminationRepprovedSubjects()));

    return $result;
  }

  public function getMarksShortStr()
  {
    $result = implode(',',array_map(create_function('$sers', 'return $sers->getShortValueString();'), $this->getStudentExaminationRepprovedSubjects()));

    return $result;
  }

  public function getLastMarkStr()
  {
    $result = array();

    foreach ($this->getStudentExaminationRepprovedSubjects() as $sers)
    {
      $result[] = $sers->getValueString();
    }

    if (count($result) == 0)
    {
      return null;
    }

    return $result[count($result) - 1];
  }

  public function getApprovalYear()
  {
    return $this->getStudentApprovedCareerSubject()->getSchoolYear()->getYear();
  }

  public function getOrderedStudentExaminationRepprovedSubjects()
  {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(StudentExaminationRepprovedSubjectPeer::MARK);
    $c->addAscendingOrderByColumn(StudentExaminationRepprovedSubjectPeer::DATE);

    return $this->getStudentExaminationRepprovedSubjects($c);
  }

  public function getResultStr()
  {
	 return $this->getMarksStr();
  }
	
  public function isApproved()
  {
    //If has the link to student_approved_career_subject means that the subject has been approved in examination
    return ! is_null($this->getStudentApprovedCareerSubject());
  }
  
  public function getMarksShortStrByCurrentSchoolYear()
  {
    $result = implode(',',array_map(create_function('$sers', 'return $sers->getShortValueString();'), $this->getStudentExaminationRepprovedSubjectsByCurrentSchoolYear()));

    return $result;
  }
  
  public function getStudentExaminationRepprovedSubjectsByCurrentSchoolYear()
  {
	  $c = new Criteria();
	  $c->addJoin(StudentExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_SUBJECT_ID,ExaminationRepprovedSubjectPeer::ID);
	  $c->addJoin(ExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_ID, ExaminationRepprovedPeer::ID);
	  $c->add(ExaminationRepprovedPeer::SCHOOL_YEAR_ID,SchoolYearPeer::retrieveCurrent());
	  
	  return $this->getStudentExaminationRepprovedSubjects($c);
	  
  }
  
  public function getCourseSubject()
  {
      return $this->getCourseSubjectStudent()->getCourseSubject();
  }
}

try { sfPropelBehavior::add('StudentRepprovedCourseSubject', array('student_approved_course_subject')); } catch(sfConfigurationException $e) {}
