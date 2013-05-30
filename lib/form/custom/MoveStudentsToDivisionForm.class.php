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
 * MoveStudentsToDivisionForm
 *
 * @author María Emilia Corrons <ecorrons@cespi.unlp.edu.ar>
 */

class MoveStudentsToDivisionForm extends sfForm
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    $this->getWidgetSchema()->setNameFormat('move_students[%s]');

    $this->configureWidgets();
    $this->configureValidators();
  }

  public function configureWidgets()
  {
    $division = $this->getOption('division');

    $divisions_criteria = new Criteria();
    $divisions_criteria->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $division->getCareerSchoolYear()->getId());
    $divisions_criteria->addAscendingOrderByColumn(DivisionPeer::YEAR);
    $divisions_criteria->add(DivisionPeer::YEAR, $division->getYear());
    $divisions_criteria->add(DivisionPeer::ID, $division->getId(), Criteria::NOT_EQUAL);
    $this->setWidget('destiny_division_id', new sfWidgetFormPropelChoice(array('criteria' => $divisions_criteria, 'model' => 'Division', 'add_empty' => true)));

    $this->getWidgetSchema()->setLabel('destiny_division_id', 'División destino');

    $criteria = new Criteria();
    $criteria->addJoin(DivisionStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
    $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID, Criteria::INNER_JOIN);
    $criteria->add(DivisionStudentPeer::DIVISION_ID, $division->getId());
    $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);

    $this->setWidget("students", new sfWidgetFormPropelChoice(array(
      "model" => "Student",
      "criteria" => $criteria,
      'peer_method' => 'doSelectActive',
      "multiple"  => true,
      "renderer_class"  => "csWidgetFormSelectDoubleList",
    )));

    $this->getWidgetSchema()->setLabel("students", "Alumnos a mover");
  }

  public function configureValidators()
  {
    $this->setValidator('destiny_division_id', new sfValidatorPropelChoice(array('model' => 'Division', 'required' => true)));

    $this->setValidator("students", new sfValidatorPropelChoice(array(
        "model" => "Student",
        "multiple" => true,
        'required' => true
      )));

    $this->mergePostValidator(new sfValidatorCallback(array(
        'callback' => array($this, 'postValidateDestinyDivision')
      )));
  }

  public function postValidateDestinyDivision(sfValidatorBase $validator, $values)
  {
    $origin_division = $this->getOption('division');
    $destiny_division = DivisionPeer::retrieveByPK($values['destiny_division_id']);
    $destiny_cssy_ids = array();

    foreach ($destiny_division->getCourses() as $destiny_course)
    {
      $destiny_cssy_ids[] = $destiny_course->getCourseSubject()->getCareerSubjectSchoolYearId();
    }

    foreach ($origin_division->getCourses() as $origin_course)
    {
      if (!in_array($origin_course->getCourseSubject()->getCareerSubjectSchoolYearId(), $destiny_cssy_ids))
      {
        $error = new sfValidatorError($validator, 'No es posible mover el/los alumno/s ya que las divisiones origen y destino elegidas difieren en sus cursos');
        throw new sfValidatorErrorSchema($validator, array('destiny_division_id' => $error));
      }
    }

    return $values;
  }
}