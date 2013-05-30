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
 * SubjectConfiguration form for BBA.
 * @subpackage form
 * @author     pmacadden
 */
class BbaSubjectConfigurationForm extends SubjectConfigurationForm
{
  public function configure()
  {
    parent::configure();
    $course_type_choices = SchoolBehaviourFactory::getInstance()->getCourseTypeChoices();
    $this->setWidget('course_type', new sfWidgetFormChoice(array(
        'choices' => $course_type_choices,
        'expanded' => true
    )));
    $this->setValidator('course_type', new sfValidatorChoice(array(
        'choices'  => array_keys($course_type_choices),
        'required' => true
    )));
    $this->setDefault('course_type',SchoolBehaviourFactory::getInstance()->getDefaultCourseType());

    $this->getWidgetSchema('attendance_type')->setHelp('attendance_type', 'Se define el tipo de asistencia que tendrán las materias. Si se cambia de "por materia" a "por día", se perderán los valores de los cursos existentes.');
    $this->widgetSchema->setHelp('course_type','Determina la cantidad de notas de un alumno dentro de la cursada y el método de evaluación.');
    unset(
      $this["course_required"],
      $this["final_examination_required"],
      $this['course_examination_count'],
      $this['course_marks'],
      $this['evaluation_method']
    );
  }

   public  function doSave($con = null)
  {
    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    parent::doSave($con);

    $this->updateObject();
    $this->object->setCourseMarks(3);
    $this->object->setEvaluationMethod(BaseCustomOptionsHolder::getInstance('CourseType')->getEvaluationMethodFor($this->object->getCourseType()));
    $this->object->save($con);

    // embedded forms
    $this->saveEmbeddedForms($con);
  }
}