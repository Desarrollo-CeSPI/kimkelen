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

class SelectValuesForAttendanceDayForm extends sfForm
{

  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    $this->widgetSchema->setNameFormat('multiple_student_attendance[%s]');
    $this->validatorSchema->setOption("allow_extra_fields", true);
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());

    $this->setWidget('career_school_year_id', new sfWidgetFormPropelChoice(array('model' => 'CareerSchoolYear', 'add_empty' => true, 'criteria' => $c)));
    $this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear','required'=>true)));

    #widget de año lectivo
    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'multiple_student_attendance_career_school_year_id',
        "message_with_no_value" => "Seleccione una carrera y apareceran los años que correspondan",
        'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));


   $this->setValidator('year', new sfValidatorString(array('required'=>true)));
    #widget division
    $division_widget = new sfWidgetFormPropelChoice(array('model' => 'Division', 'add_empty' => true));
    $this->setWidget('division_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $division_widget,
        'observe_widget_id' => 'multiple_student_attendance_year',
        "message_with_no_value" => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getDivisions')
      )));

    $this->setValidator('division_id', new sfValidatorPropelChoice(array('required' => true, 'model' => 'Division')));

    #widget Fecha
    $this->setWidget('day', new csWidgetFormDateInput());
    $this->setValidator('day', new mtValidatorDateString(array('required' => true,'date_output'=>'Y-m-d')));
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

  public function setDay($day)
  {
    $this->day = $day;
    $this->setDefault('day', $this->day);
  }

}