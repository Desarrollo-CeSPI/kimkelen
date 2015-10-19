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
 * Description of CourseSubjectPathwayMarksForm
 *
 */
class CourseSubjectPathwayMarksForm extends BaseCourseSubjectForm
{
	public function configure()
	{
		$widgets    = array();
		$validators = array();

		$options = array(
			'min'      => $this->getMinimumMark(),
			'max'      => $this->getMaximumMark(),
			'required' => false
		);

		$messages = array(
			'min'     => 'La calificación debe ser al menos %min%.',
			'max'     => 'La calificación debe ser a lo sumo %max%.',
			'invalid' => 'El valor ingresado es inválido.'
		);

		$this->disableCSRFProtection();
		foreach ($this->object->getCourseSubjectStudentPathways() as $course_subject_student)
		{
				$widget_name = $course_subject_student->getId();

			  $widgets[$widget_name] = new sfWidgetFormInput(array('default' => $course_subject_student->getMark()), array('class' => 'mark'));
        $validators[$widget_name] = new sfValidatorPass();
			  $this->getWidgetSchema()->setHelp($widget_name, 'Para libre ingrese 0 (cero)');
		}

		$this->setWidgets($widgets);
		$this->setValidators($validators);

		$this->widgetSchema->setNameFormat('course_student_mark['.$this->object->getId().'][%s]');
	}


	public function getJavaScripts()
	{
		return array_merge(parent::getJavaScripts(),array('course_subject_student_mark.js'));
	}

	protected function getMinimumMark()
	{
		return SchoolBehaviourFactory::getEvaluatorInstance()->getMinimumMark();
	}

	protected function getMaximumMark()
	{
		return SchoolBehaviourFactory::getEvaluatorInstance()->getMaximumMark();
	}

	protected function doSave($con = null)
	{
		$values = $this->getValues();

		foreach ($this->object->getCourseSubjectStudentPathways() as $course_subject_student)
		{
				$value = $values[$course_subject_student->getId()];
			  if (($value == "")){
				  $value = null;
			  }
				$course_subject_student->setMark($value);
				$course_subject_student->save($con);
		}
	}
}