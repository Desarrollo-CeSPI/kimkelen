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
class CommissionForm extends BaseCourseForm
{

  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset'));

    unset($this["is_closed"], $this["division_id"], $this["current_period"], $this['related_division_id']);

    $this->setWidget('starts_at', new csWidgetFormDateInput());
    $this->setValidator('starts_at', new mtValidatorDateString());

    $this->widgetSchema->moveField("starts_at", "after", "quota");

    $this->widgetSchema["school_year_id"] = new sfWidgetFormInputHidden();
    $this->setDefault("school_year_id", SchoolYearPeer::retrieveCurrent()->getId());

    $school_year = SchoolYearPeer::retrieveCurrent();
    $career_school_year_criteria = new Criteria();
    $career_school_year_criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $this->setWidget('career_school_year_id', new sfWidgetFormPropelChoice(array('criteria' => $career_school_year_criteria, 'model' => 'CareerSchoolYear', 'add_empty' => true)));
    $this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear', 'column' => 'id')));

    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'course_career_school_year_id',
        "message_with_no_value" => "Seleccione una carrera y apareceran los años que correspondan",
        'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));

    $this->setValidator('year', new sfValidatorInteger());

    $courses_choices = new sfWidgetFormPropelChoice(array(
        'model' => 'CareerSubjectSchoolYear',
        'multiple' => false,
      ));

    $this->setWidget('subject', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $courses_choices,
        'observe_widget_id' => 'course_year',
        "message_with_no_value" => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getCourses')
      )));


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
      $criteria = new Criteria();
      $criteria->add(CareerSubjectPeer::IS_OPTION,false);
      $criteria->addJoin(CareerSubjectPeer::ID,CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
      $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
      $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID,SubjectPeer::ID);
      $criteria->addAscendingOrderByColumn(SubjectPeer::NAME);
      $this->setWidget('subject', new sfWidgetFormPropelChoice(array(
      'model'     => 'CareerSubjectSchoolYear',
      //      'model'     => 'Subject',
      //      'peer_method' => 'doSelectOrderedAndActive',
      'criteria'  => $criteria,
      'multiple'  => false,
      'add_empty' => true
      )));
     */
    $this->setValidator('subject', new sfValidatorPropelChoice(array('model' => 'CareerSubjectSchoolYear', 'multiple' => false, 'required' => true)));

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
    $c2 = new Criteria();
    $c2->add(CareerSubjectPeer::YEAR, $values);
    $c2->add(CareerSubjectPeer::HAS_OPTIONS, false);
    $c2->addAnd(CareerSubjectPeer::IS_OPTION, false);

    $c2->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    $c2->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);


    $c2->addSelectColumn(CareerSubjectSchoolYearPeer::ID);
    $stmt2 = CareerSubjectSchoolYearPeer::doSelectStmt($c2);
    $no_options = $stmt2->fetchAll();


    $c= new Criteria();
    $c->add(CareerSubjectPeer::YEAR, $values);
    $c->add(CareerSubjectPeer::HAS_OPTIONS, false);
    $c->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $c->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
    $c->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    $c->addJoin(OptionalCareerSubjectPeer::CHOICE_CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->clearSelectColumns();

    $c->addSelectColumn(CareerSubjectSchoolYearPeer::ID);
    $stmt = CareerSubjectSchoolYearPeer::doSelectStmt($c);
    $options = $stmt->fetchAll();


    $all = array_merge($options,$no_options);
    $choice = array();
    foreach ($all as $echa){
      $choice[] =  $echa['ID'];
    }

    $criteria = new Criteria();
    $criteria->add(CareerSubjectSchoolYearPeer::ID,$choice,Criteria::IN);
    $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID,  CareerSubjectPeer::ID);
    $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
    $criteria->addAscendingOrderByColumn(SubjectPeer::NAME);

    $widget->setOption('criteria', $criteria);

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
        $this->setDefault('subject', $course_subjects[0]->getCareerSubjectSchoolYearId());
        $this->setDefault('career_school_year_id', $course_subjects[0]->getCareerSubjectSchoolYear()->getCareerSchoolYearId());
        $this->setDefault('year', $course_subjects[0]->getCareerSubject()->getYear());
      }
    }

  }

  protected function doSave($con = null)
  {
    $con = (is_null($con)) ? $this->getConnection() : $con;
    parent::doSave($con);

    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }
    $this->saveSubject($con);

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

    $value = $this->getValue('subject');
    if ($value)
    {
      try
      {
        $con->beginTransaction();
        $career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByPK($value);
        $career_subject_school_year->createCourseSubject($this->getObject());
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