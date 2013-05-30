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

class MultipleSchoolYearRegistrationForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      "school_year_id" => new sfWidgetFormInputHidden(),
      "shift_id" => new sfWidgetFormPropelChoice(array('model' => 'Shift', 'add_empty' => false))
    ));
    
    $this->setValidators(array(
      "school_year_id" => new sfValidatorPropelChoice(array('model' => 'SchoolYear', 'column' => 'id')),
      "shift_id" => new sfValidatorPropelChoice(array('model' => 'Shift', 'column' => 'id'))
    ));
    
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    
    $this->widgetSchema->setNameFormat('multiple_school_year_registration[%s]');
    
    $this->validatorSchema->setOption("allow_extra_fields", true);
  }
  
  public function setStudentsIds($students_ids)
  {
    foreach ($students_ids as $student_id)
    {
      $student = StudentPeer::retrieveByPK($student_id);
      
      $this->setWidget("student_$student_id", new mtWidgetFormPlain(array(
        "object" => $student,
        "add_hidden_input" => true,
        "use_retrieved_value" => false
      )));
      
      $this->setValidator("student_$student_id", new sfValidatorPropelChoice(array(
        "model" => "Student",
        "required" => true
      )));
      
      $this->setDefault("student_$student_id", $student_id);
      $this->widgetSchema->setLabel("student_$student_id", "Student");
      
      $this->widgetSchema->moveField("shift_id", "after", "student_$student_id");
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
      $con = Propel::getConnection();
    }

    try
    {
      $con->beginTransaction();

      $values = $this->getValues();
      
      $school_year = SchoolYearPeer::retrieveByPK($values["school_year_id"]);
      $shift = ShiftPeer::retrieveByPK($values["shift_id"]);
      
      unset($values["school_year_id"]);
      unset($values["shift_id"]);
      
      foreach ($values as $student_id)
      {
        $student = StudentPeer::retrieveByPk($student_id);
        
        if (!$student->getIsRegistered($school_year))
        {
          $student->registerToSchoolYear($school_year, $shift, $con);
        }
      }

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();

      throw $e;
    }

  }
}