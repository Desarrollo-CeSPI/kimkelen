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

class CopyStudentsFromOtherCourseForm extends BaseFormPropel
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $years = array();
    foreach ($this->getObject()->getCourseSubjects() as $course_subject)
    {
      $years[] = $course_subject->getCareerSubjectSchoolYear()->getCareerSubject()->getYear();
    }

    $c = new Criteria();
    $c->add(CoursePeer::SCHOOL_YEAR_ID, $this->getObject()->getSchoolYearId());
    $c->add(CoursePeer::DIVISION_ID, null, Criteria::ISNULL);
    $c->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID, Criteria::INNER_JOIN);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID, Criteria::INNER_JOIN);
    $c->add(CareerSubjectPeer::YEAR, $years, Criteria::IN);

    $this->setWidget('course_id', new sfWidgetFormPropelChoice(array(
      'model' => 'Course',
      'criteria' => $c
    )));

    $this->getWidgetSchema()->setLabel('course_id', 'Comisión');

    $this->setValidator('course_id', new sfValidatorPropelChoice(array(
      'model' => 'Course',
      'criteria' => $c
    )));

    $this->getWidgetSchema()->setNameFormat('course_students_copy[%s]');
  }

  public function getModelName()
  {
    return 'Course';
  }

  protected function doSave($con = null)
  {
    $con = (is_null($con)) ? $this->getConnection() : $con;

    $course = $this->getObject();

    $from_course = CoursePeer::retrieveByPK($this->values['course_id']);

    try
    {
      $con->beginTransaction();

      foreach ($from_course->getStudents() as $student)
      {
        foreach ($course->getCourseSubjects() as $course_subject)
        {
          $course_subject_student = new CourseSubjectStudent();
          $course_subject_student->setCourseSubjectId($course_subject->getId());
          $course_subject_student->setStudentId($student->getId());
          $course_subject_student->save($con);
        }
      }
      
      $con->commit();
    }
    catch (Exception $e)
    {
      throw $e;
      $con->rollBack();
    }
  }
}