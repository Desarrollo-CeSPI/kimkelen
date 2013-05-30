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
 * StudentReportsFormFilter
 *
 * @author María Emilia Corrons <ecorrons@cespi.unlp.edu.ar>
 */
class StudentReportsForm extends SelectValuesForAttendanceDayForm
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    $this->getWidgetSchema()->setNameFormat('student_reports[%s]');

    $this->configureWidgets();
    $this->configureValidators();

        $this->mergePostValidator(new sfValidatorCallback(array(
        'callback' => array($this, 'postValidateShift')
      )));
  }

  public function configureWidgets()
  {    
    $this->setWidget('career_school_year_id', 
      new sfWidgetFormPropelChoice(array(
        'model' => 'CareerSchoolYear', 
        'add_empty' => true)));

    $this->setWidget('shift_id', new sfWidgetFormPropelChoice(array('model' => 'Shift', 'add_empty' => true)));

    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'student_reports_career_school_year_id',
        "message_with_no_value" => "Seleccione una carrera y aparecerán los años que correspondan",
        'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));

    $division_widget = new sfWidgetFormPropelChoice(array('model' => 'Division', 'add_empty' => true));
    $this->setWidget('division_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $division_widget,
        'observe_widget_id' => 'student_reports_year',
        "message_with_no_value" => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getDivisions')
      )));    
  }

  public function configureValidators()
  {
    $this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear', 'required' => false)));
    $this->setValidator('year', new sfValidatorString(array('required' => false)));
    $this->setValidator('division_id', new sfValidatorPropelChoice(array('model' => 'Division', 'required' => false)));
    $this->setValidator('shift_id', new sfValidatorPropelChoice(array('model' => 'Shift', 'required' => false)));
  }

  public static function getYears($widget, $values)
  {
    $career = CareerSchoolYearPeer::retrieveByPk($values)->getCareer();
    $choices = $career->getYearsForOption(true);
    $widget->setOption('choices', $choices);
    sfContext::getInstance()->getUser()->setAttribute('career_school_year_id', $values);
  }

  public static function getDivisions($widget, $values)
  {
    $sf_user = sfContext::getInstance()->getUser();
    $career_school_year_id = $sf_user->getAttribute('career_school_year_id');
    $c = new Criteria();
    $c->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $c->add(DivisionPeer::YEAR, $values);

    if ($sf_user->isPreceptor())
    {
      PersonalPeer::joinWithDivisions($c, $sf_user->getGuardUser()->getId());
    }

    $division_ids = array();
    foreach (DivisionPeer::doSelect($c) as $division)
    {
      if ($division->hasAttendanceForDay())
        $division_ids[] = $division->getId();
    }
    $criteria = new Criteria();
    $criteria->add(DivisionPeer::ID, $division_ids, Criteria::IN);

    $widget->setOption('criteria', $criteria);

  }

  public function postValidateShift(sfValidatorBase $validator, $values)
  {
    if ($values['shift_id'] && !$values['career_school_year_id'])
    {
      $error = new sfValidatorError($validator, 'You cant generate reports with only shift field chosen');
      throw new sfValidatorErrorSchema($validator, array('shift_id' => $error));
    }

    return $values;
  }
}