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

class BbaMultipleSubjectConfigurationForm extends MultipleSubjectConfigurationForm
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

    $this->widgetSchema->setHelp('course_type','Determina la cantidad de notas de un alumno dentro de la cursada y el método de evaluación.');

    unset(
      $this['max_disciplinary_sanctions'],
      $this["max_previous"]
    );
  }


  public function save($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (is_null($con))
    {
      $con = Propel::getConnection(CareerSubjectSchoolYearPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    try
    {
      $con->beginTransaction();

      $values = $this->getValues();

      // TODO: ver si se puede mejorar esto:
      $fields = array(        
        "final_examination_required",
        "course_required",
        "course_minimun_mark",
        "course_examination_count",
        "max_previous",
        "evaluation_method",
        "course_type",
        "attendance_type"
      );
      $to_save = array();

      foreach ($fields as $field)
      {
        $to_save[$field] = isset($values[$field]) ? $values[$field] : null;
        unset($values[$field]);
      }

      foreach ($values as $career_subject_school_year_id)
      {        
        $career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByPk($career_subject_school_year_id, $con);

        $subject_configuration = $career_subject_school_year->getSubjectConfigurationOrCreate();

        foreach ($to_save as $field => $value)
        {
          if (!is_null($value))
          {
            $method = "set".sfInflector::camelize($field);
            $subject_configuration->$method($value);
          }
        }

        $career_subject_school_year->setSubjectConfiguration($subject_configuration);
        $subject_configuration->setCourseMarks(BaseCustomOptionsHolder::getInstance('CourseType')->getMarksFor($subject_configuration->getCourseType()));
        $subject_configuration->setEvaluationMethod(BaseCustomOptionsHolder::getInstance('CourseType')->getEvaluationMethodFor($subject_configuration->getCourseType()));
        $subject_configuration->save($con);
      }

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }

    return $this->object;
  }
}