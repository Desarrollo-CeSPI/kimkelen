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
 * StudentCareerSchoolYear form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class StudentCareerSchoolYearForm extends BaseStudentCareerSchoolYearForm
{
  public function configure()
  {
	$sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
   
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');
   
    unset($this['created_at'], $this['career_school_year_id'], $this['is_processed'] , $this['id'], $this['year']);
  
	$this->setWidget('student_id', new sfWidgetFormInputHidden());
	$status = BaseCustomOptionsHolder::getInstance('StudentCareerSchoolYearStatus')->getOptionsSelect();

	$this->setWidget('status',  new sfWidgetFormSelect(array('choices'  => $status)));
    
    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('change_status_motive_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'student_career_school_year_status',
        'message_with_no_value' => 'Seleccione un estado y aparecerán los motivos correspondientes',
        'get_observed_value_callback' => array(get_class($this), 'getMotives')
      )));
   
	$this->setValidators(array(
      'student_id'              => new sfValidatorPropelChoice(array('model' => 'Student', 'column' => 'id', 'required' => false)),
      'status'   		        => new sfValidatorChoice(array('choices' => array_keys($status))),
      'change_status_motive_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ChangeStatusMotive','column' => 'id')),
    ));
  }
  
  public static function getMotives($widget, $values){
	
	$motives = ChangeStatusMotivePeer::getMotivesByStatusId($values);
	$choices = array();
	
	foreach ($motives as $m):
		$choices[$m->getId()] = $m->getName(); 
	endforeach;	
	
    $widget->setOption('choices', $choices);
  }
}
    
