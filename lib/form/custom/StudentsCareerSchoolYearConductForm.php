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
 * StudentCareerSchoolYearConduct form base class.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Ivan
 */
class StudentsCareerSchoolYearConductForm extends sfForm
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");

    $this->widgetSchema->setNameFormat('student_school_year_conduct[%s]');

    $this->validatorSchema->setOption("allow_extra_fields", true);
  }

public function getStudents()
{
  return $this->students;
}

public function getPeriods()
{
  return $this->periods;
}
public function getCareerSchoolYear()
{
  return $this->career_school_year;
}

public function setStudents($students, $career_school_year)
{
   $this->students = $students;
   $this->career_school_year =$career_school_year;
   $csp = CareerSchoolYearPeriodPeer:: getPeriodsSchoolYear($career_school_year->getId());
   $this->periods = $csp;

    foreach ($students as $student)
    {
        $student_id = $student->getId();
        //$student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $career_school_year);
        $this->setWidget("student_$student_id", new mtWidgetFormPlain(array(
          "object" => $student,
          "add_hidden_input" => false,
          "use_retrieved_value" => false
        )));
        $this->setValidator("student_$student_id", new sfValidatorPass( ));
        //$this->setDefault("student_$student_id", $student_id);
        $this->widgetSchema->setLabel("student_$student_id", "Student");
        foreach($csp as $period)
        {
          $name = "conduct_".$student->getId()."_".$period->getId();
          $student_conduct = $student->getConductPeriod($period);

              if ($period->getIsClosed())
              {
                $this->setWidget($name, new mtWidgetFormPlain(array(
                  "object" => $student_conduct,
                  "add_hidden_input" => false,
                  "use_retrieved_value" => false
                    )));
                $this->setValidator($name, new sfValidatorPass());
               }
              else
              {
                 $this->setWidget($name, new sfWidgetFormPropelChoice(array('model'=> 'Conduct', 'add_empty' => true)));
                 $this->setValidator($name, new sfValidatorPropelChoice(array('model' => 'Conduct', 'required' => false)));
                 if($student_conduct)
                 {
                    $this->setDefault($name, $student_conduct->getConductId());
                 }
              }
            # $this->widgetSchema->setLabel("conduct_$student_id", "Conducta");
          }
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
      $students=$this->getStudents();
      $periods=$this->getPeriods();
      $career_school_year = $this->getCareerSchoolYear();
      foreach ($students as $student)
      {
        $student_career_school_year =  StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $career_school_year);
        foreach ($periods as $period)
        {
          if (!$period->getIsClosed())
           {
              $conduct_id = $values['conduct_'. $student->getId() . '_' . $period->getId()];
              $scsyc = StudentCareerSchoolYearConductPeer::retrieveOrCreate($student_career_school_year, $period);
              if(!is_null($conduct_id))
              {
                $scsyc->setConductId($conduct_id);
                $scsyc->save();
              }
              elseif (!is_null($scsyc->getConduct())) {
                  $scsyc->delete();
              }
            }
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