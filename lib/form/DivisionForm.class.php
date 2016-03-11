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
 * Division form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class DivisionForm extends BaseDivisionForm
{

  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset', 'Tag', 'Url', 'Javascript', 'I18N'));

    if ($this->getObject()->isNew())
    {
      $this->setWidget('division_title_ids', new sfWidgetFormPropelChoice(array(
          'model' => 'DivisionTitle',
          'multiple' => true,
          "renderer_class" => "csWidgetFormSelectDoubleList",
        )));

      $this->setValidator('division_title_ids', new sfValidatorPropelChoice(array('model' => 'DivisionTitle', 'column' => 'id', 'multiple' => true)));

      unset($this['division_title_id']);
      //$this->setValidator('division_title_ids',new sfValidatorPass());

    }
    else
    {
      $this->getWidget('division_title_id')->setOption('add_empty', true);
    }

    $school_year = SchoolYearPeer::retrieveCurrent();
    $criteria = new Criteria();
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $criteria->add(CareerSchoolYearPeer::IS_PROCESSED, false);
    $this->getWidget('career_school_year_id')->setOption('criteria', $criteria);
    $this->getWidget('career_school_year_id')->setOption('add_empty', true);
    $this->getWidget('shift_id')->setOption('add_empty', true);

    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'division_career_school_year_id',
        "message_with_no_value" => "Seleccione una carrera y apareceran los años que correspondan",
        'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));

    $courses_choices = new sfWidgetFormPropelChoice(array(
        'model' => 'CareerSubjectSchoolYear',
        'multiple' => true,
        "renderer_class" => "csWidgetFormSelectDoubleList",
      ));

    $this->setWidget('division_courses', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $courses_choices,
        'observe_widget_id' => 'division_year',
        "message_with_no_value" => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getCourses')
      )));

    $this->setValidator('division_courses', new sfValidatorPass());

	  //$this->getWidgetSchema()->setLabel('division_title_ids', __('Division'));
  }

  public static function getYears($widget, $values)
  {
    $career = CareerSchoolYearPeer::retrieveByPk($values)->getCareer();
    $choices = $career->getYearsForOption(true);
    $widget->setOption('choices', $choices);
    sfContext::getInstance()->getUser()->setAttribute('career_school_year_id', $values);

  }

  public static function getCourses($widget, $values)
  {
    $career_school_year_id = sfContext::getInstance()->getUser()->getAttribute('career_school_year_id');
    $criteria = new Criteria();
    $criteria->add(CareerSubjectPeer::YEAR, $values);
    $criteria->add(CareerSubjectPeer::IS_OPTION, false);
    $criteria->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    $criteria->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
    CareerSubjectSchoolYearPeer::sorted($criteria);
    $widget->setOption('criteria', $criteria);

  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    $values = array();
    foreach ($this->getObject()->getCourses() as $course)
    {
      foreach ($course->getCourseSubjects() as $course_subject)
      {
        $values[] = $course_subject->getCareerSubjectSchoolYearId();
      }
    }
    $this->setDefault('division_courses', $values);

  }

  protected function doSave($con = null)
  {
    //parent::doSave($con);

    $con = is_null($con) ? Propel::getConnection() : $con;
    $values = $this->getValues();

    try
    {
      $con->beginTransaction();

      if (array_key_exists('division_courses', $values))
      {
        if (array_key_exists('division_title_ids', $values))
        {
          $division_title_ids = $values['division_title_ids'];
          foreach ($division_title_ids as $division_title_id)
          {
            if (DivisionPeer::checkUnique($division_title_id, $values['career_school_year_id'], $values['year']))
            {
              $division = new Division();
              $division->setDivisionTitleId($division_title_id);
              $division->setCareerSchoolYearId($values['career_school_year_id']);
              $division->setShiftId($values['shift_id']);
              $division->setYear($values['year']);
              $this->createPreceptor($division, $con);

              if ($this->getObject()->isNew())
              {
                foreach ($this->values['division_courses'] as $career_subject_school_year_id)
                {
                  $division->createCourse($career_subject_school_year_id, $con);
                }
              }
              $division->save($con);
            }
          }
        }
      }

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollback();
      throw $e;
    }

  }

  public function getUser()
  {
    return sfContext::getInstance()->getUser();

  }

  public function createPreceptor($division, $con)
  {
    if ($this->getUser()->isPreceptor() && $division->isNew())
    {
      $division_preceptor = new DivisionPreceptor();
      $division_preceptor->setDivision($division);
      $preceptor = PersonalPeer::retrievePreceptorBySfGuardUserId($this->getUser()->getGuardUser()->getId());
      $division_preceptor->setPreceptorId($preceptor->getId());
      $division_preceptor->save($con);
    }

  }

  public function getFormFieldsDisplay()
  {
    return array('division_title_ids', 'career_school_year_id', 'year', 'division_courses', 'shift_id');

  }

}