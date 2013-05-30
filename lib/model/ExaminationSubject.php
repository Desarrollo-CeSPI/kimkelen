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

class ExaminationSubject extends BaseExaminationSubject
{

  public function canBeClosed()
  {
    $c = new Criteria();
    $c->addJoin(ExaminationSubjectPeer::ID, CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID);
    $c->add(CourseSubjectStudentExaminationPeer::MARK, null, Criteria::ISNULL);
    $c->add(CourseSubjectStudentExaminationPeer::IS_ABSENT, false);
    $c->add(CourseSubjectStudentExaminationPeer::CAN_TAKE_EXAMINATION, TRUE);

    return $this->countCourseSubjectStudentExaminations($c) == 0 && !$this->getIsClosed();

  }

  public function getMessageCantBeClosed()
  {
    if ($this->getIsClosed())
    {
      return "The examination subject can't be closed because it's already closed.";
    }
    else
    {
      return 'Examination subject cant be closed, because some students were not calificated';
    }

  }

  public function close(PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    try
    {
      $con->beginTransaction();

      $c = new Criteria();
      $c->add(CourseSubjectStudentExaminationPeer::CAN_TAKE_EXAMINATION, TRUE);

      foreach ($this->getCourseSubjectStudentExaminations($c) as $course_subject_student_examination)
      {
        $course_subject_student_examination->close($con);
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

  /**
   * This method join Students with this examination_subject
   *
   * @return array Students
   */
  public function getStudents()
  {
    $criteria = new Criteria();
    $criteria->add(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, $this->getId());
    $criteria->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID, Criteria::INNER_JOIN);
    $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);

    return StudentPeer::doSelect($criteria);

  }

  public function canManageStudents()
  {
    return !$this->getIsClosed();

  }

  public function getMessageCantManageStudents()
  {
    if ($this->getIsClosed())
    {
      return "The examination subject cant be moddify because it's closed.";
    }

  }

  public function getSortedCourseSubjectStudentExaminations(Criteria $c = null)
  {
    if (is_null($c))
    {
      $c = new Criteria();
    }

    $c->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
    $c->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $c->addAscendingOrderByColumn(PersonPeer::LASTNAME);

    return $this->getCourseSubjectStudentExaminations($c);

  }

  public function getSubject()
  {
    return $this->getCareerSubjectSchoolYear()->getCareerSubject()->getSubject();

  }

  public function getExaminationNoteForStudent($student)
  {
    $criteria = new Criteria();
    $criteria->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $criteria->add(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, $this->getId());
    $criteria->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    return CourseSubjectStudentExaminationPeer::doSelectOne($criteria);

  }

  public function getTeachers()
  {
    return array_map(create_function('$c', 'return $c->getTeacher();'), $this->getExaminationSubjectTeachers());

  }

  public function getTeachersToString()
  {
    return implode(' / ', $this->getTeachers());
  }

  public function canEditCalifications()
  {
    return !$this->getIsClosed();// || sfContext::getInstance()->getUser()->hasCredential('edit_closed_examination');
  }

}

sfPropelBehavior::add('ExaminationSubject', array('examination_subject'));