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
 * CourseSubjectConfigurationFIrstForm
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class CourseSubjectConfigurationFirstForm extends sfFormPropel
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

//    $cscs = CourseSubjectConfigurationPeer::retrieveBySubject($this->getObject());
    $c = new Criteria();
    if(!is_null($this->getObject()->getCareerSubjectSchoolYear()->getSubjectConfiguration()))
    {
        $c->add(CareerSchoolYearPeriodPeer::COURSE_TYPE, $this->getObject()->getCareerSubjectSchoolYear()->getSubjectConfiguration()->getCourseType());
    }else{
         $c->add(CareerSchoolYearPeriodPeer::COURSE_TYPE, CourseType::QUATERLY);
    }
    
    $c->add(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_ID, $this->getObject()->getCareerSubjectSchoolYear()->getCareerSchoolYearId());
    $this->setWidget('quaterly_id', new sfWidgetFormPropelChoice(array('model' => 'CareerSchoolYearPeriod', 'criteria' => $c)));
    $this->setValidator('quaterly_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYearPeriod')));
    $config = $this->getObject()->getCourseSubjectConfigurations();

    $this->setDefault('quaterly_id', $this->getObject()->getQuaterlyPeriod());
    $this->getWidgetSchema()->setLabel('quaterly_id', 'Period');
  }

  public function getModelName()
  {
    return 'CourseSubject';
  }

  public function setCourseType($course_type)
  {
    $this->course_type = $course_type;
  }

  public function getCourseType()
  {
    return $this->course_type;
  }

  public function doSave($con = null)
    {
    if (is_null($con))
    {
      $con = $this->getConnection();
    }
    $course_subject = $this->getObject();
    $values = $this->getValues();

    $c = new Criteria();

    if ($this->getCourseType() == CourseType::BIMESTER)
    {
      $c->add(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_PERIOD_ID, $values['quaterly_id']);  
    }
    else
    {
      $c->add(CareerSchoolYearPeriodPeer::ID, $values['quaterly_id']);  
    }
     

    $periods = CareerSchoolYearPeriodPeer::doSelect($c);

    try
    {
      $con->beginTransaction();
      $cscs = CourseSubjectConfigurationPeer::retrieveBySubject($course_subject);

      foreach ($cscs as $csc)
      {
        $csc->delete($con);
      }

      foreach ($periods as $period)
      {
        $csc = new CourseSubjectConfiguration();
        $csc->setCourseSubject($course_subject);
        $csc->setCareerSchoolYearPeriod($period);
        $csc->save($con);
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }
  }

}