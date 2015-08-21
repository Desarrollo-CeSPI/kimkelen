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
 * SubjectForPathwayCommissionForm
 *
 * @author María Emilia Corrons <ecorrons@cespi.unlp.edu.ar>
 */
class SubjectForPathwayCommissionForm extends SubjectForCommissionForm
{
	public function configure()
	{
		sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset'));

		unset($this["starts_at"], $this["name"], $this["quota"], $this["is_closed"], $this["current_period"], $this["related_division_id"], $this["division_id"], $this["is_pathway"]
		);


		$career_school_year_criteria = new Criteria();
		$career_school_year_criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $this->getObject()->getSchoolYearId());
		$this->setWidget('career_school_year_id', new sfWidgetFormPropelChoice(array('criteria' => $career_school_year_criteria, 'model' => 'CareerSchoolYear', 'add_empty' => true)));
		$this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear', 'column' => 'id')));

		$w = new sfWidgetFormChoice(array('choices' => array()));
		$this->setWidget('year', new dcWidgetAjaxDependence(array(
			'dependant_widget' => $w,
			'observe_widget_id' => 'course_career_school_year_id',
			"message_with_no_value" => "Seleccione una carrera y apareceran los años que correspondan",
			'get_observed_value_callback' => array(get_class($this), 'getYears')
		)));

		$this->setValidator('year', new sfValidatorInteger());

		$courses_choices = new sfWidgetFormPropelChoice(array(
			'model' => 'CareerSubjectSchoolYear',
			'multiple' => false,
		));

		$this->setWidget('subject', new dcWidgetAjaxDependence(array(
			'dependant_widget' => $courses_choices,
			'observe_widget_id' => 'course_year',
			"message_with_no_value" => "Seleccione una carrera y un año",
			'get_observed_value_callback' => array(get_class($this), 'getCourses')
		)));


		$this->setValidator('subject', new sfValidatorPropelChoice(array('model' => 'CareerSubjectSchoolYear', 'multiple' => false, 'required' => true)));
	}


}