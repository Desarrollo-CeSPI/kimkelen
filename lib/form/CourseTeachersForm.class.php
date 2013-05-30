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

class CourseTeachersForm extends BaseFormPropel
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $criteria = new Criteria();
    $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);
    $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
    $this->setWidget("teachers", new sfWidgetFormPropelChoice(array(
      "model" => "Teacher",
      "criteria" => $criteria,
      'peer_method' => 'doSelectActive',
      "multiple"  => true,
      "renderer_class"  => "csWidgetFormSelectDoubleList",
    )));

    $this->getWidgetSchema()->setLabel("teachers", "Profesores");

    $this->setValidator("teachers" , new sfValidatorPropelChoice(array(
      "model" => "Teacher",
      "multiple" => true,
      'required' => false
    )));

    $this->getWidgetSchema()->setNameFormat('course_teachers[%s]');
  }

  public function getModelName()
  {
    return 'Course';
  }

  public function updateDefaultsFromObject()
  {
    $course= $this->getObject();

    $values = array();
    foreach ($course->getTeachers() as $teacher)
    {
      $values[] = $teacher->getId();
    }
    $this->setDefault("teachers", $values);
  }

  protected function doSave($con = null)
  {    
    $course = $this->getObject();

    $con = (is_null($con)) ? $this->getConnection() : $con;
    try
    {
      $con->beginTransaction();

      foreach ($course->getCourseSubjects () as $course_subject)
      {
        foreach ($course_subject->getCourseSubjectTeachers() as $course_subject_teacher)
        {
          $course_subject_teacher->delete($con);
        }
      }

      if (isset($this->values["teachers"]))
      {
        foreach ($this->values["teachers"] as $teacher_id)
        {
          foreach ($course->getCourseSubjects () as $course_subject)
          {
            $course_subject_teacher = new CourseSubjectTeacher();
            $course_subject_teacher->setTeacherId($teacher_id);
            $course_subject_teacher->setCourseSubject($course_subject);
            $course_subject->save($con);
          }
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