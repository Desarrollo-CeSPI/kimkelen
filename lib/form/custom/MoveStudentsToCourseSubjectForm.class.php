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
 * MoveStudentsToCourseSubjectForm
 *
 */

class MoveStudentsToCourseSubjectForm extends sfForm
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    $this->getWidgetSchema()->setNameFormat('move_students[%s]');

    $this->configureWidgets();
    $this->configureValidators();
  }

  public static function getCriteriaForAvailableStudentsForCourseIds($course_subject)
  {
    $ret = array();
    foreach ($course_subject->getStudents() as $st)
    {
      $ret[] = $st->getId();
    }

    $criteria = new Criteria();
    $criteria->add(StudentPeer::ID,$ret,Criteria::IN);

    return $criteria;
  }

  public function configureWidgets()
  {
    $course_subject = $this->getOption('course_subject');
    $course_subjects_criteria = new Criteria();
    $course_subjects_criteria->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $course_subject->getCareerSubjectSchoolYearId(), Criteria::EQUAL);
       $course_subjects_criteria->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID, Criteria::INNER_JOIN);
    $course_subjects_criteria->add(CoursePeer::SCHOOL_YEAR_ID, $course_subject->getCourse()->getSchoolYearId());
    $course_subjects_criteria->add(CoursePeer::ID, $course_subject->getCourse()->getId(), Criteria::NOT_EQUAL);

    $this->setWidget('destiny_course_subject_id', new sfWidgetFormPropelChoice(array('criteria' => $course_subjects_criteria, 'model' => 'CourseSubject', 'add_empty' => true, 'method' => 'getCourseSubjectAndTeacherToString')));

    $this->getWidgetSchema()->setLabel('destiny_course_subject_id', 'Comisión destino');

    $available_criteria = self::getCriteriaForAvailableStudentsForCourseIds($course_subject);

    $this->setWidget('students', new csWidgetFormStudentMany(array('criteria'=> $available_criteria)));

    $this->getWidgetSchema()->setLabel("students", "Alumnos a mover");
  }

  public function configureValidators()
  {
    $this->setValidator('students', new sfValidatorPass());

    $this->setValidator('destiny_course_subject_id', new sfValidatorPropelChoice(array('model' => 'CourseSubject', 'required' => true)));

    $this->setValidator("students", new sfValidatorPropelChoice(array(
        "model" => "Student",
        "multiple" => true,
        'required' => true
      )));
  }
}