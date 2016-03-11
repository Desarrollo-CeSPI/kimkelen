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

class StudentApprovedCareerSubject extends BaseStudentApprovedCareerSubject
{

  public function getCareerStudent(PropelPDO $con = null)
  {
    $c = new Criteria();
    $c->add(CareerStudentPeer::STUDENT_ID, $this->getStudentId());
    $c->add(CareerStudentPeer::CAREER_ID, $this->getCareerSubject()->getCareerId());
    return CareerStudentPeer::doSelectOne($c);

  }

  /**
   * Can be deleted if the student hastn  approved a career_subject that has for correlative $this.
   *
   * @param PropelPDO $con
   * @return boolean
   */
  public function canDelete(PropelPDO $con = null)
  {
    $career_subjects = $this->getCareerSubject()->getCareerSubjectsCorrelatives($con);

    foreach ($career_subjects as $career_subject)
    {
      $c = new Criteria();
      $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $this->getStudentId());
      $c->add(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID, $career_subject->getId());

      if (StudentApprovedCareerSubjectPeer::doCount($c))
        return false;
    }

    return true;

  }

  public function getMessageCantDelete()
  {
    return 'The equivalence cant be deleted, becouse the student has approved some career_subject that have this for correlative.';

  }

  public function getResult($with_mark = true)
  {
    return ($with_mark) ? 'Aprobado ' . $this->getMark() : 'Aprobado';

  }

  public function getMethod()
  {
    return ($this->getIsEquivalence()) ? 'Equivalencia' : 'Final';

  }

  public function renderChangeLog()
  {
    return ncChangelogRenderer::render($this, 'tooltip', array('credentials' => 'view_changelog'));

  }

  public function getYear()
  {
    return $this->getCareerSubject()->getYear();

  }

  public function getApprovationDate()
  {
    return StudentApprovedCareerSubjectPeer::retrieveApprovationDate($this);

  }

  public function hasStudentApprovedCourseSubject()
  {
    return count(StudentApprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($this)) > 0;

  }

  public function hasStudentDisapprovedCourseSubject()
  {
    return count(StudentDisapprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($this)) > 0;

  }

  public function hasStudentRepprovedCourseSubject()
  {
    return count(StudentRepprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($this))> 0 ;
  }

  public function getApprovationInstance()
  {
    // Caso Regular: aprueba en primer instancia
    $instance = StudentApprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($this);
    if(!is_null($instance))
    {
        return $instance;
    }
	
    // Caso Mesa de Diciembre, Marzo
    $instance = StudentDisapprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($this);

    if(!is_null($instance))
    {
      return $instance;
    }

    // Caso de previa
    $instance = StudentRepprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($this);
    if(!is_null($instance))
    {
      return $instance;
    }

    return null;
  }

}

sfPropelBehavior::add('StudentApprovedCareerSubject', array('student_approved_career_subject'));
