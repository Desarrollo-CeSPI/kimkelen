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

class ExaminationRepprovedSubject extends BaseExaminationRepprovedSubject
{

  public function getMessageCantAddStudents()
  {
    return 'Cant add students because the examination repproved subject is closed.';
  }

  public function canCalificate(PropelPDO $con = null)
  {
    return $this->countStudentExaminationRepprovedSubjects() && !$this->getIsClosed();
  }

  public function getMessageCantCalificate()
  {

    if ($this->getIsClosed())
      $str =  'Cant calificate students because the examination repproved subject is closed.';
    else
      $str = 'Cant calificate students because the examination repproved subject dont have students inscripted';

    return $str;
  }

  public function canBeClosed(PropelPDO $con = null)
  {
    if ($this->getIsClosed())
      return false;

    if ($this->countStudentExaminationRepprovedSubjects() == 0)
      return false;

    $c = new Criteria();
    $c->add(StudentExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_SUBJECT_ID, $this->getId());
    $criterion= $c->getNewCriterion(StudentExaminationRepprovedSubjectPeer::MARK,null,Criteria::ISNOTNULL);
    $criterion->addOr($c->getNewCriterion(StudentExaminationRepprovedSubjectPeer::IS_ABSENT, true));
    $c->add($criterion);

    return StudentExaminationRepprovedSubjectPeer::doCount($c);
  }

  public function getMessageCantBeClosed()
  {
    if ($this->getIsClosed())
      return 'It cant be closed because the examination repproved subject has already been closed.';

    if ($this->countStudentExaminationRepprovedSubjects() == 0)
      return 'It cant be closed because the examination repproved subject dont have students inscripted';

    return "It cant be closed because not all the students had been calificated";
  }

  /**
  * This method returns the students related with this examinationRepprovedSubject
  *
  * @return array Students[]
  */
  public function getStudents()
  {
    $c = new Criteria();
    $c->add(StudentExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_SUBJECT_ID, $this->getId());
    $c->addJoin(StudentExaminationRepprovedSubjectPeer::STUDENT_REPPROVED_COURSE_SUBJECT_ID, StudentRepprovedCourseSubjectPeer::ID);
    $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID,  StudentPeer::ID);
    return StudentPeer::doSelect($c);
  }

  /**
   * This method closes the examination_repproved_subject and the student_examination_repproved_subjects related.
   *
   * @param PropelPDO $con
   */
  public function close(PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    try
    {
      $con->beginTransaction();

      foreach ($this->getStudentExaminationRepprovedSubjects(null, $con) as $student_examination_repproved_subject)
      {
        $student_examination_repproved_subject->close($con);
      }

      $this->setIsClosed(true);
      $this->save($con);

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }
  }

  public function canDelete()
  {
    if ($this->countStudentExaminationRepprovedSubjects() > 0)
      return false;

    return !$this->getIsClosed();
  }

  public function getMessageCantDelete()
  {
    if ($this->countStudentExaminationRepprovedSubjects() > 0)
      return 'No puede borrarse por que tiene alumnos inscriptos';
  }

  public function canEditStudents()
  {
    return !$this->getIsClosed();
  }

  public function canEditCalifications()
  {
    return !$this->getIsClosed();// || sfContext::getInstance()->getUser()->hasCredential('edit_closed_examination');
  }

  public function getExaminationNoteForStudent($student) {

    $student_repproved_course_subject = StudentRepprovedCourseSubjectPeer:: retrieveByCareerSubjectIdAndStudentId($this->getCareerSubject()->getId(), $student->getId());
    $c = new Criteria();
    $c->add(StudentExaminationRepprovedSubjectPeer::STUDENT_REPPROVED_COURSE_SUBJECT_ID, $student_repproved_course_subject->getId());
    $c->add(StudentExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_SUBJECT_ID, $this->getId());

    return StudentExaminationRepprovedSubjectPeer::doSelectOne($c);
  }

    public function getTeachersToString()
  {
    return implode(' / ', $this->getExaminationRepprovedSubjectTeachers());
  }
}

sfPropelBehavior::add('ExaminationRepprovedSubject', array('examination_repproved_subject'));