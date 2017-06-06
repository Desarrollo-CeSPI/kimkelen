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
class AnexaStudentsCareerSchoolYearConductForm extends StudentsCareerSchoolYearConductForm
{ 
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
                $c = new Criteria();
                $c->add(ConductPeer::SHIFT_ID, $this->getDivision()->getShift()->getId());
                  
                $this->setWidget($name, new sfWidgetFormPropelChoice(array('model'=> 'Conduct', 'add_empty' => true,'criteria' => $c)));
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
}