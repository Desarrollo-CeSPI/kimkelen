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
 * CareerStudent form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class CareerStudentForm extends BaseCareerStudentForm
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    unset( $this['created_at'], $this['status'],$this['file_number'], $this['graduation_school_year_id']);
    //career choice widget
    $this->getWidget('career_id')->setOption('criteria',SchoolBehaviourFactory::getInstance()->getAvailableCareerForStudentCriteria($this->getObject()->getStudent()));

    $this->setWidget('student_id', new sfWidgetFormInputHidden());

    $w = new sfWidgetFormChoice(array("choices" => array()));
    $this->setWidget("start_year", new dcWidgetAjaxDependence(array(
      "dependant_widget"            => $w,
      "observe_widget_id"           => "career_student_career_id",
      "message_with_no_value"       => "Seleccione una carrera y apareceran los años que correspondan",
      "get_observed_value_callback" => array(get_class($this), "getYears")
    )));

    $this->setWidget("orientation_id", new dcWidgetAjaxDependence(array(
      "dependant_widget"            => $w,
      "observe_widget_id"           => "career_student_career_id",
      "message_with_no_value"       => "Seleccione una carrera y apareceran las orientaciones correspondientes",
      "get_observed_value_callback" => array(get_class($this), "getOrientations")
    )));

    $this->setWidget("sub_orientation_id", new dcWidgetAjaxDependence(array(
      "dependant_widget"            => $w,
      "observe_widget_id"           => "career_student_orientation_id",
      "message_with_no_value"       => "Seleccione una orientación y apareceran las sub orientaciones correspondientes",
      "get_observed_value_callback" => array(get_class($this), "getSubOrientations")
    )));

    $this->validatorSchema["start_year"] = new sfValidatorInteger();
  }

  /**
   * Don't call parent::doSave($con) we have to use Propelconnection to safely get nextFileNumber
   * @param <type> $con
   */
  protected function doSave($con = null)
  {
    if (is_null($con))
    {
      $con = $this->getConnection();
    }
    $this->updateObject();

    $start_year = $this->getValue("start_year");


    SchoolBehaviourFactory::getInstance()->setStudentFileNumberForCareer($this->getObject(),$con);
    $this->object->save($con);

    SchoolBehaviourFactory::getInstance()->createStudentCareerSubjectAlloweds($this->object, $start_year , $con);

    // embedded forms
    $this->saveEmbeddedForms($con);
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

  public static function getSubOrientations($widget, $value)
  {
    $orientation = OrientationPeer::retrieveByPk($value);
    $sub_orientations = array("" => "");
    foreach ($orientation->getSubOrientations() as $sub_orientation)
    {
      $sub_orientations[$sub_orientation->getId()] = $sub_orientation;
    }

    $widget->setOption("choices", $sub_orientations);
  }
}