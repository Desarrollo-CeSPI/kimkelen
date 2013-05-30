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
 * CourseSubjectTeacher form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class CourseSubjectTeacherForm extends BaseCourseSubjectTeacherForm
{

  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset', 'Tag', 'Url','Javascript'));

    parent::configure();

    unset(
      $this['id'],
      $this['course_subject_id'],
      $this['teacher_id']
    );

    $course_subject = $this->getObject();
    
    if (!$course_subject->getCareerSubject()->getHasOptions())
    {
      $id = "course_subject_".$course_subject->getId();
      $this->widgetSchema[$id] = new mtWidgetFormPlain(array(
        "object" => $course_subject,
        "method" => "getCareerSubject"
        ));

      $this->widgetSchema->setLabel($id, "Materia");

      $this->validatorSchema[$id] = new sfValidatorPass();

      $this->widgetSchema[$id."_teachers"] = new sfWidgetFormPropelChoice(array(
        "model" => "Teacher",
        'peer_method' => 'doSelectActive',
        "multiple"  => true,
        "renderer_class"  => "csWidgetFormSelectDoubleList",
      ));

      $this->widgetSchema->setLabel($id."_teachers", "Profesores");

      $this->validatorSchema[$id."_teachers"] = new sfValidatorPropelChoice(array(
        "model" => "Teacher",
        "multiple" => true,
        'required' => false
      ));
    }
    $this->widgetSchema->setNameFormat('course_subject_teacher['.$this->object->getId().'][%s]');
    $this->validatorSchema->setOption("allow_extra_fields", true);
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();
    $course_subject = $this->getObject();

    if (!$course_subject->getCareerSubject()->getHasOptions())
    {
      $id = "course_subject_".$course_subject->getId()."_teachers";
      $values = array();
      foreach ($course_subject->getCourseSubjectTeachers() as $course_subject_teacher)
      {
        $values[] = $course_subject_teacher->getTeacherId();
      }

      $this->setDefault($id, $values);
    }
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);
    $course_subject = $this->getObject();

    if (!$course_subject->getCareerSubject()->getHasOptions())
    {
      $id = "course_subject_".$course_subject->getId()."_teachers";
      foreach ($course_subject->getCourseSubjectTeachers() as $course_subject_teacher)
      {
        $course_subject_teacher->delete($con);
      }

      if (isset($this->values[$id]))
      {
        foreach ($this->values[$id] as $teacher_id)
        {
          $course_subject_teacher = new CourseSubjectTeacher();
          $course_subject_teacher->setTeacherId($teacher_id);
          $course_subject_teacher->setCourseSubject($course_subject);
          $course_subject->save($con);
        }
      }
    }
  }

  public function getModelName() {
    return 'CourseSubject';
  }

  public function getJavaScripts()
  {
    return array_merge(parent::getJavaScripts(), array('/sfFormExtraPlugin/js/double_list.js'));
  }
}