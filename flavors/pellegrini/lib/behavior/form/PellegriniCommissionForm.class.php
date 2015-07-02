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
 * CommissionCourse form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class PellegriniCommissionForm extends CommissionForm
{
	public function configure()
	{
	  parent::configure();

    $courses_choices = new sfWidgetFormPropelChoice(array(
        'model' => 'CareerSubjectSchoolYear',
        'multiple' => true,
        "renderer_class"  => "csWidgetFormSelectDoubleList",
    ));

    $this->setWidget('subject', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $courses_choices,
        'observe_widget_id' => 'course_year',
        "message_with_no_value" => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getCourses')
    )));

    $this->setValidator('subject', new sfValidatorPropelChoice(array(
    	'model' => 'CareerSubjectSchoolYear', 
    	'multiple' => true, 
    	'required' => true
    )));
	}

	protected function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();
    if (isset($this->widgetSchema['subject']))
    {
      $course_subjects = array();

      foreach ($this->getObject()->getCourseSubjects() as $course_subject)
      {
        $course_subjects[] = $course_subject;
      }

      if (isset($course_subjects[0]))
      {
        
        $this->setDefault('career_school_year_id', $course_subjects[0]->getCareerSubjectSchoolYear()->getCareerSchoolYearId());
        $this->setDefault('year', $course_subjects[0]->getCareerSubject()->getYear());

       	foreach ($course_subjects as $course_subject)
      	{
        	$values[] = $course_subject->getCareerSubjectSchoolYearId();
      	}
      	
      	$this->setDefault('subject', $values);
      }
    }

  }

	public function saveSubject(PropelPDO $con)
  {
    if (!isset($this->widgetSchema['subject']))
    {
      // somebody has unset this widget
      return;
    }

    foreach ($this->getObject()->getCourseSubjects() as $course_subject)
    {
      $course_subject->delete($con);
    }

    $values = $this->getValue('subject');
    
    if ($values)
    {
      try
      {
        $con->beginTransaction();
        foreach ($values as $value)
        {
          $career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByPK($value);
          $career_subject_school_year->createCourseSubject($this->getObject(), $con);  
        }
        
        $con->commit();
      }
      catch (Exception $e)
      {
        throw $e;
        $con->rollBack();
      }
    }

  }
}