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
 * CareerSchoolYearPeriod form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class CareerSchoolYearPeriodForm extends BaseCareerSchoolYearPeriodForm
{
  public function configure()
  {
    unset($this['is_closed']);

    // agrego esto porque hay unas funciones que buscan al periodo por su nombre. Ver si
    // se pueden mejorar dichas funciones. - están en Student.php, lineas 607 y 624 aprox.

    $this->getWidgetSchema()->setHelp('name', 'Ingrese: Primer Trimestre, Segundo Trimestre, Tercer Trimestre, Primer Cuatrimestre, Segundo Cuatrimestre, Primer Bimestre o Segundo Bimestre');

    $this->setWidget('career_school_year_id', new sfWidgetFormInputHidden());

    $this->getWidget('career_school_year_period_id')->setLabel( 'Parent');
    $this->getWidgetSchema()->setHelp('career_school_year_period_id', 'Periodo padre contenedor, seleccionar solo cuando es un periodo bimestral');

	  $this->setWidget('start_at', new csWidgetFormDateInput());
    $this->setValidator('start_at', new mtValidatorDateString());

    $this->setWidget('end_at', new csWidgetFormDateInput());
    $this->setValidator('end_at', new mtValidatorDateString());
    
    $course_type_choices = SchoolBehaviourFactory::getInstance()->getCourseTypeChoices();
    $this->setWidget('course_type', new sfWidgetFormChoice(array('choices' => $course_type_choices)));
    $this->setValidator('course_type', new sfValidatorChoice(array('choices' => array_keys($course_type_choices), 'required' => true)));

    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkParent'))));

  }

  public function checkParent($validator, $values)
  {
    if ($values['course_type'] != CourseType::BIMESTER && !is_null($values['career_school_year_period_id']))
    {
      $error = new sfValidatorError($validator, 'No se puede seleccionar un periodo padre si no es un periodo bimestral');
      // throw an error bound to the password field
      throw new sfValidatorErrorSchema($validator, array('career_school_year_period_id' => $error));
    }
    elseif ($values['course_type'] == CourseType::BIMESTER && is_null($values['career_school_year_period_id']))
    {
      $error = new sfValidatorError($validator, 'Si el periodo es bimestral, es obligatorio seleccionar un periodo padre.');
      // throw an error bound to the password field
      throw new sfValidatorErrorSchema($validator, array('career_school_year_period_id' => $error));
    }

    return $values;

  }

	public function setParentWidget($career_school_year_id)
	{

		$c = new Criteria();
		$c->add(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
		
    $this->setWidget('career_school_year_period_id', new sfWidgetFormPropelChoice(array(
			                                                                                   'model'     => 'CareerSchoolYearPeriod',
			                                                                                   'criteria' => $c,
                                                                                         'add_empty' => true
		                                                                                    )));

    $this->setValidator('career_school_year_period_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYearPeriod', 'column' => 'id', 'required' => false)));
  }
}