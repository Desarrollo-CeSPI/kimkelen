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
 * Description of StudentOrientationForm
 *
 * @author gramirez
 */
class StudentOrientationForm extends sfFormPropel
{

  public function configure()
  {
    //Set the formatter to the form
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $c = new Criteria();
    $c->add(CareerStudentPeer::STUDENT_ID, $this->getObject()->getId());
    $c->addJoin(CareerStudentPeer::CAREER_ID, CareerPeer::ID);
    
    $this->setWidget('career_id' , new sfWidgetFormPropelChoice(array('model' => 'Career', 'add_empty' => true ,'criteria' => $c)));
    $this->setValidator('career_id', new sfValidatorPropelChoice(array('model' => 'Career', 'required' => true)));

    $w = new sfWidgetFormPropelChoice(array(
      'model' => 'Orientation',
      'add_empty' => true
    ));

    $w2 = new sfWidgetFormPropelChoice(array(
      'model' => 'SubOrientation',
      'add_empty' => true
    ));
    
    $this->setWidget("orientation_id" , new dcWidgetAjaxDependence(array(
        "dependant_widget" => $w,
        "observe_widget_id" => "student_orientation_career_id",
        "message_with_no_value" => "Seleccione una carrera y apareceran las orientaciones que correspondan",
        "get_observed_value_callback" => array(get_class($this), "getOrientations"
    ))));
    $this->setValidator("orientation_id", new sfValidatorPropelChoice(array("model" => "Orientation", "required" => false)));
    
    $this->setWidget('sub_orientation_id', new dcWidgetAjaxDependencePropel(array(
        'dependant_widget' => $w2,
        'observe_widget_id' => 'student_orientation_orientation_id',
        'message_with_no_value' => "Seleccione una orientación",
        'related_column' => 'orientation_id'
    )));    
    $this->setValidator("sub_orientation_id", new sfValidatorPropelChoice(array("model" => "SubOrientation", "required" => false)));

    $this->widgetSchema->setNameFormat('student_orientation[%s]');

    $this->setWidget('orientation_change_observations', new sfWidgetFormTextarea(array("label"=>'Observations')));
    $this->setValidator('orientation_change_observations', new sfValidatorString(array('required'=>false)));

   }

  
  public function getModelName()
  {
    return 'Student';
  }

  public static function getOrientations($widget, $value)
  {
    $career = CareerPeer::retrieveByPk($value);

    $orientations = array('' => '');
    $c = new Criteria();
    $c->setDistinct();
    
    foreach ($career->getOrientations($c) as $orientation)
    {
      $orientations[$orientation->getId()] = $orientation->__toString();
    }

    $widget->setOption("choices", $orientations);    
  }

  protected function doSave($con = null)
  {
    $career_id = $this->getValue('career_id');
    $student_career = CareerStudentPeer::retrieveByCareerAndStudent($career_id, $this->getObject()->getId());
    $student_career->setOrientationId($this->getValue('orientation_id'));
    $student_career->setSubOrientationId($this->getValue('sub_orientation_id'));

    $student_career->setOrientationChangeObservations($this->getValue('orientation_change_observations'));
    $student_career->save($con);
  }
}