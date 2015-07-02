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

class MultipleSubjectConfigurationForm extends sfForm
{

  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");

    $this->widgetSchema->setNameFormat('multiple_subject_configuration[%s]');

    $this->validatorSchema->setOption("allow_extra_fields", true);

    $this->mergeForm(new SubjectConfigurationForm());

    unset(
      $this["id"],
      $this["course_required"],
      $this["max_previous"],
      $this["final_examination_required"],
      $this['course_examination_count'],
      $this['evaluation_method']
    );
  }

  public function setCareerSubjectSchoolYearsIds($career_subject_school_years_ids)
  {
    foreach ($career_subject_school_years_ids as $career_subject_school_year_id)
    {
      $career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByPK($career_subject_school_year_id);

      $this->setWidget("career_subject_school_year_$career_subject_school_year_id", new mtWidgetFormPlain(array(
        "object" => $career_subject_school_year,
        "add_hidden_input" => true,
        "use_retrieved_value" => false
      )));

      $this->setValidator("career_subject_school_year_$career_subject_school_year_id", new sfValidatorPropelChoice(array(
        "model" => "CareerSubjectSchoolYear",
        "required" => true
      )));

      $this->setDefault("career_subject_school_year_$career_subject_school_year_id", $career_subject_school_year_id);
      $this->widgetSchema->setLabel("career_subject_school_year_$career_subject_school_year_id", "Subject");

      $this->widgetSchema->moveField("career_subject_school_year_$career_subject_school_year_id", "before", "course_minimun_mark");

      $this->widgetSchema->moveField("career_subject_school_year_$career_subject_school_year_id", "before", "course_marks");
    }
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
        "course_marks",
        "final_examination_required",
        "course_required",
        "course_minimun_mark",
        "course_examination_count",
        "max_previous",
        "evaluation_method",
        "max_disciplinary_sanctions",
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