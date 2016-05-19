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
   
    unset($this['created_at'], $this['career_school_year_id'], $this['is_processed'] , $this['id']);
   
    $max = CareerPeer::getMaxYear();
    $years = array();
    for ($i = 1; $i <= $max; $i++)
      $years[$i] = $i;
     
	$this->setWidget('student_id', new sfWidgetFormInputHidden());
	$this->setWidget('year', new sfWidgetFormChoice(array('choices' => $years)));
	$this->setWidget('status',  new sfWidgetFormSelect(array('choices'  => BaseCustomOptionsHolder::getInstance('StudentCareerSchoolYearStatus')->getOptionsSelect())));
	$this->setWidget('observations', new sfWidgetFormTextarea());
   
	$this->setValidators(array(
      'student_id'       => new sfValidatorPropelChoice(array('model' => 'Student', 'column' => 'id', 'required' => false)),
      'status'   		 => new sfValidatorString(array('max_length' => 50)),
      'year'		     => new sfValidatorChoice(array('choices' => array_keys($years))),
      'observations'     => new sfValidatorString(array('required' => false)),
    ));
  }
}
