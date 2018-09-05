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
 * Description of LvmStudentFormFilter
 *
 * @author gramirez
 */
class BbaStudentFormFilter extends StudentFormFilter
{

  public function configure() {
   
   parent::configure();
    $this->unsetFields();
    
	$c_criteria = new Criteria(CareerPeer::DATABASE_NAME);
	$this->setWidget('career', new sfWidgetFormPropelChoice(array('model' => 'Career', 'criteria' => $c_criteria, 'add_empty' => true)));
    $this->setValidator('career', new sfValidatorPropelChoice(array('model' => 'Career', 'criteria' => $c_criteria, 'required' => false)));
	
	$w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'student_filters_career',
        'message_with_no_value' => 'Seleccione una carrera',
        'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));
      
    $this->getWidgetSchema()->setHelp('year', 'El año filtra de acuerdo al año lectivo elegido.');
    $this->getWidgetSchema()->moveField('career', sfWidgetFormSchema::BEFORE, 'year');
  }
  
  public function unsetFields()
  {
    unset(
      $this['global_file_number'],
      $this['person_id'],
      $this['folio_number'],
      $this['order_of_merit'],
      $this['occupation_id'],
      $this['busy_starts_at'],
      $this['busy_ends_at'],
      $this['student_career_subject_allowed_list'],
      $this['student_career_subject_allowed_pathway_list'],
      $this['blood_group'],
      $this['blood_factor'],
      $this['emergency_information'],
      $this['health_coverage_id'],
      $this['order_of_merit'],
      $this['folio_number'],
      $this['origin_school_id'],
      $this['educational_dependency'],
      $this['judicial_restriction']    
    );
  }
  public function getFields()
  {
    return array_merge(parent::getFields(),
      array(
        'student' => 'Text',
        'school_year'=> 'Number',
        'career'=> 'Number',
        'year' => 'Number',
        'division' => 'Number',
        'is_matriculated' => 'Boolean',
        'is_inscripted_in_career' => 'Boolean',
        'is_free_in_some_period' => 'Boolean',
        'is_graduated' => 'Boolean',
        'disciplinary_sanction_count' => 'Number',
        'status' => 'Number',
        'health_info' => 'Text'));
  }

  public static function getYears($widget, $values){
	
	$career = CareerPeer::retrievebyPk($values);
	$max = $career->getMaxYear();

	$years = array('' => '');
    for ($i = 1; $i <= $max; $i++)
      $years[$i] = $i;
    $widget->setOption('choices', $years);
  }
  
  public function addCareerColumnCriteria(Criteria $criteria , $field, $values)
  {
    if ($values)
    {
      $criteria->add(CareerStudentPeer::CAREER_ID, $values);
      $criteria->addJoin(CareerStudentPeer::STUDENT_ID, StudentPeer::ID);
      $criteria->addJoin(CareerStudentPeer::CAREER_ID,CareerSchoolYearPeer::CAREER_ID);
      //$criteria->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    }
  }
}
