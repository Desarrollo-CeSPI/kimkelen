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
 * Student form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class StudentForm extends BaseStudentForm
{
  public function configure()
  {
    $this->unsetFields();

    $person = $this->getObject()->getPerson();
    if (is_null($person))
    {
      $person = new Person();
      $this->getObject()->setPerson($person);
    }

    $personForm = new PersonForm($person, array('related_class'=>'student','embed_as'=>'person'));
    $this->embedMergeForm('person',$personForm);

    $this->getWidgetSchema()->setLabel('occupation_id','Occupation');

    $this->getWidget('student_tag_list')->setOption('expanded', true);
    $this->getWidget('student_tag_list')->setOption('multiple', true);

    $this->getWidgetSchema()->setHelp('folio_number', __('Format must be XX-XXXX'));
    $this->getWidgetSchema()->setHelp('order_of_merit', __('Format must be XX-XXXX'));
	/*
	$c = new Criteria();
    $c->addAscendingOrderByColumn('name');
    $c->add(StatePeer::COUNTRY_ID,Country::ARGENTINA);
    
    $this->setWidget('origin_school_state_id', new sfWidgetFormPropelchoice(array(
        'model'     => 'State',
        'add_empty' => true,
        'criteria' => $c
    )));
	$this->setValidator('origin_school_state_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'State', 'column' => 'id')));
	
	$c= new Criteria();
	$c->addAscendingOrderByColumn('name');
	
	$widget_origin_school_department = new sfWidgetFormPropelChoice(array(
      'model'      => 'Department',
      'add_empty'  => true,
      'criteria' => $c
    ));
    
    $this->setWidget('origin_school_department_id', new dcWidgetAjaxDependencePropel(array(
        'related_column'     => 'state_id',
        'dependant_widget'   => $widget_origin_school_department,
        'observe_widget_id'  => 'origin_school_state_id',
        'message_with_no_value' => __('Select a state first'),
        )));
        
	$this->setValidator('origin_school_department_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Department', 'column' => 'id')));
	
	*/
	$this->setWidget('origin_school_state_id',  new sfWidgetFormSelect(array('choices'  => StatePeer::retrieveByCountryId(Country::ARGENTINA))));
	$this->setValidator('origin_school_state_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'State', 'column' => 'id')));
	
	$d = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('origin_school_department_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $d,
        'observe_widget_id' => 'student_origin_school_state_id',
        'message_with_no_value' => 'Seleccione una provincia y apareceran los partidos correspondientes.',
        'get_observed_value_callback' => array(get_class($this), 'getDepartments')
      )));
	$this->setValidator('origin_school_department_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Department', 'column' => 'id')));
	
	$c = new sfWidgetFormChoice(array('choices' => array()));
	$this->setWidget('origin_school_city_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $d,
        'observe_widget_id' => 'student_origin_school_department_id',
        'message_with_no_value' => 'Seleccione un partido y aparecerán las ciudades correspondientes.',
        'get_observed_value_callback' => array(get_class($this), 'getCities')
      )));
	$this->setValidator('origin_school_city_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'City', 'column' => 'id')));

	
	//$this->setWidget('origin_school_id', new dcWidgetFormPropelJQuerySearch(array('model' => 'OriginSchool', 'column' => array('name'),'peer_method'=> 'doSelect')));
	$o = new sfWidgetFormChoice(array('choices' => array()));
	$this->setWidget('origin_school_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $o,
        'observe_widget_id' => 'student_origin_school_city_id',
        'message_with_no_value' => 'Seleccione una ciudad y aparecerán las escuelas correspondientes.',
        'get_observed_value_callback' => array(get_class($this), 'getOriginSchools')
      )));
	$this->setValidator('origin_school_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'OriginSchool', 'column' => 'id')));

	$this->getWidgetSchema()->setLabel('origin_school_id','Origin school');
	$this->validatorSchema->setOption("allow_extra_fields", true);
  }

  public function unsetFields()
  {
    unset($this['person_id']);
  }

  public function getFormFieldsDisplay()
  {
    $personal_data_fields = array('person-lastname', 'person-firstname', 'person-identification_type', 'person-identification_number', 'person-sex', 'global_file_number','origin_school_state_id' ,'origin_school_department_id','origin_school_city_id','origin_school_id', 'person-cuil', 'person-birthdate', 'person-birth_country', 'person-birth_state','person-birth_department', 'person-birth_city', 'person-photo', 'person-observations' );

    if($this->getObject()->getPerson()->getPhoto())
    {
      $personal_data_fields = array_merge($personal_data_fields, array('person-current_photo', 'person-delete_photo'));
    }

    return array(
          'Personal data'   =>  $personal_data_fields,
          'Contact data'    =>  array('person-email', 'person-phone', 'person-address'),
          'Health data'   =>  array('blood_group', 'blood_factor', 'health_coverage_id', 'emergency_information'),
          'Tags' => array('student_tag_list'),
    );
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);
    $guard_user = $this->getObject()->getPersonSfGuardUser();
    if ( !is_null($guard_user))
    {   $student_group = BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::STUDENT);
        if ( ! array_key_exists( $student_group,$guard_user->getGroups()) )
        {
          $guard_user->addGroupByName($student_group);
          $guard_user->save($con);
        }
    }
  }
  
  public static function getDepartments($widget, $values){
	
	$departments = DepartmentPeer::retrieveByStateId($values);
	$choices = array();
	$choices[0] = '';
	foreach ($departments as $d):
		$choices[$d->getId()] = $d->getName(); 
	endforeach;	
	
    $widget->setOption('choices', $choices);
  }
  
   public static function getCities($widget, $values){
	
	$cities = CityPeer::retrieveByDepartmentId($values);
	$choices = array();
	$choices[0] = '';
	foreach ($cities as $c):
		$choices[$c->getId()] = $c->getName(); 
	endforeach;	
	
    $widget->setOption('choices', $choices);
  }
  
   public static function getOriginSchools($widget, $values){
	
	$schools = OriginSchoolPeer::retrieveByCityId($values);
	$choices = array();
	
	$choices[0] = $avlues;

	foreach ($schools as $s):
		$choices[$s->getId()] = $s->getName() . ' - ' . $s->getAddress(); 
	endforeach;	
	
	
    $widget->setOption('choices', array_unique($choices));
  }
  
  
}
