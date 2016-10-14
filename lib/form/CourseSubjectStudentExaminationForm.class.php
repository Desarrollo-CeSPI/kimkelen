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
 * CourseSubjectStudentExamination form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class CourseSubjectStudentExaminationForm extends BaseCourseSubjectStudentExaminationForm
{
  public function configure()
  {
    unset(
      $this["course_subject_student_id"],
      $this["examination_subject_id"],
      $this["examination_number"],
      $this["can_take_examination"]
    );
    
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');
    
    $configuration = $this->object->getExaminationSubject()->getCareerSubjectSchoolYear()->getConfiguration();
    
    if (!$this->getObject()->getExaminationSubject()->canEditCalifications())
    {
      unset($this["is_absent"]);
      $this->widgetSchema["mark"] = new mtWidgetFormPlain(array(
              'object' => $this->getObject(), 'method' => 'getMarkStrByConfig',"empty_value" => "Is absent", 'method_args' => $configuration, 'add_hidden_input' => false),
               array('class' => 'mark'));
    }
    else
    {
		$this->widgetSchema->setHelp("mark", "Enter student's mark or mark him as absent.");
      
		if(! $configuration->isNumericalMark())
		{
			$letter_mark = LetterMarkPeer::getLetterMarkByValue((Int)$this->getObject()->getMark());
			$this->setWidget('mark',new sfWidgetFormPropelChoice(array('model'=> 'LetterMark', 'add_empty' => true)));
			
			if(!is_null($letter_mark)) {
				$this->setDefault('mark', $letter_mark->getId());
			 }
			
			$this->setValidator('mark',new sfValidatorPropelChoice(array('model' => 'LetterMark', 'required' => false)));
		}
		else
		{
			$behavior = SchoolBehaviourFactory::getEvaluatorInstance();
			$this->validatorSchema["mark"]->setOption("min", $behavior->getMinimumMark());
			$this->validatorSchema["mark"]->setOption("max", $behavior->getMaximumMark());
			
			$this->validatorSchema["mark"]->setMessage("min", "Mark should be at least %min%.");
			$this->validatorSchema["mark"]->setMessage("max", "Mark should be at most %max%.");
		}
    }
     
    $this->widgetSchema->setLabel("mark", $this->getObject()->getCourseSubjectStudent()->getStudent());   
    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array("callback" => array($this, "validateAbsence"))));

    $this->setWidget('date', new csWidgetFormDateInput());
    $this->getWidget('date')->setLabel('Fecha');
    $this->setValidator('date', new mtValidatorDateString(array('required' => false)));
  }
  
  public function validateAbsence($validator, $values)
  {
    if ($values["is_absent"] && !is_null($values["mark"]))
    {
      throw new sfValidatorError($validator, "The student can't be absent and have a mark.");
    }
    
    return $values;
  }
  
}
