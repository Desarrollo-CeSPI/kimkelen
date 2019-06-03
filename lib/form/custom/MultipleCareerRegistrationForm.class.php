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

class MultipleCareerRegistrationForm extends sfForm
{
  
  public function configure()
  {
    $w = new sfWidgetFormChoice(array("choices" => array()));
    
    $w2 = new sfWidgetFormPropelChoice(array(
      'model' => 'SubOrientation',
      'add_empty' => true
    ));

    $criteria = new Criteria();
    $criteria->addJoin(CareerPeer::ID, CareerSchoolYearPeer::CAREER_ID);
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $criteria->add(CareerSchoolYearPeer::IS_PROCESSED, false);
    $this->setWidgets(array(
      "career_id" => new sfWidgetFormPropelChoice(array("model" => "Career" , 'criteria' => $criteria)),
      "orientation_id" => new dcWidgetAjaxDependence(array(
        "dependant_widget" => $w,
        "observe_widget_id" => "multiple_career_registration_career_id",
        "message_with_no_value" => "Seleccione una carrera y apareceran las orientaciones que correspondan",
        "get_observed_value_callback" => array(get_class($this), "getOrientations"))),
      "start_year" => new dcWidgetAjaxDependence(array(
        "dependant_widget"            => $w,
        "observe_widget_id"           => "multiple_career_registration_career_id",
        "message_with_no_value"       => "Seleccione una carrera y apareceran los años que correspondan",
        "get_observed_value_callback" => array(get_class($this), "getYears"))),
      'sub_orientation_id' => new dcWidgetAjaxDependencePropel(array(
        'dependant_widget' => $w2,
        'observe_widget_id' => 'multiple_career_registration_orientation_id',
        'related_column' => 'orientation_id'
      )),
      "admission_date" => new csWidgetFormDateInput()  
    ));
    
    $this->setValidators(array(
      "career_id" => new sfValidatorPropelChoice(array("model" => "Career", "required" => true)),
      "orientation_id" => new sfValidatorPropelChoice(array("model" => "Orientation", "required" => false)),
      "sub_orientation_id" => new sfValidatorPropelChoice(array("model" => "SubOrientation", "required" => false)),
      "start_year" => new sfValidatorInteger(),
      "admission_date" => new mtValidatorDateString(array("required" => false))
    ));
    
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    
    $this->widgetSchema->setNameFormat('multiple_career_registration[%s]');
    
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
      
      $this->widgetSchema->moveField("career_id", "after", "student_$student_id");
    }
    
    $this->widgetSchema->moveField("start_year", "after", "career_id");
    $this->widgetSchema->moveField("orientation_id", "after", "career_id");
    $this->widgetSchema->moveField("sub_orientation_id", "after", "orientation_id");
    $this->widgetSchema->moveField("admission_date", "after", "start_year");
  }
  
  public function save($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (is_null($con))
    {
      $con = Propel::getConnection(CareerStudentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    try
    {
      $con->beginTransaction();

      $values = $this->getValues();
      
      $career = CareerPeer::retrieveByPK($values["career_id"]);
      $orientation = OrientationPeer::retrieveByPK($values["orientation_id"]);
      $sub_orientation = SubOrientationPeer::retrieveByPK($values["sub_orientation_id"]);
      $start_year = $values["start_year"];
      $admission_date = $values["admission_date"];
      
      unset($values["career_id"]);
      unset($values["orientation_id"]);
      unset($values["sub_orientation_id"]);
      unset($values["start_year"]);
      unset($values["admission_date"]);

      foreach ($values as $student_id)
      {
        $student = StudentPeer::retrieveByPk($student_id,$con);
        
        if (!$student->isRegisteredToCareer($career, $con))
        {                    
          $student->registerToCareer($career, $orientation, $sub_orientation, $start_year, $admission_date, $con);        
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
  
  public static function getYears($widget, $value)
  {
    $career = CareerPeer::retrieveByPk($value);
    $choices = $career->getYearsForOption(true);
    $widget->setOption("choices", $choices);
  }

  public static function getOrientations($widget, $value)
  {
    $career = CareerPeer::retrieveByPk($value);
    $orientations = array('' => '');
    foreach ($career->getOrientations() as $orientation)
    {
      $orientations[$orientation->getId()] = $orientation->__toString();
    }
    
    $widget->setOption("choices", $orientations);
  }
}