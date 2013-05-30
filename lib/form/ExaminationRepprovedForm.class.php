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
 * ExaminationRepproved form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class ExaminationRepprovedForm extends BaseExaminationRepprovedForm
{
  public function configure()
  {
    $this->widgetSchema["examination_number"] = new sfWidgetFormInputHidden();
    $this->widgetSchema["school_year_id"] = new sfWidgetFormInputHidden();

    $this->setWidget('date_from', new csWidgetFormDateInput());
    $this->setWidget('date_to', new csWidgetFormDateInput());

    $this->setValidator('date_from', new mtValidatorDateString());
    $this->setValidator('date_to', new mtValidatorDateString());

    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare(
      "date_from",
      sfValidatorSchemaCompare::LESS_THAN_EQUAL,
      "date_to",
      array(),
      array("invalid" => "Date from must be lesser than date to.")
    ));

    $options_without = $this->getWithoutExaminationTypes();
    if(!$this->getObject()->isNew() && in_array($this->getObject()->getExaminationType(), $options_without))
    {
      unset($options_without[array_search($this->getObject()->getExaminationType(), $options_without)]);
    }
    //field sex widget and validator
    $this->setWidget('examination_type', new sfWidgetFormSelect(array(
      'choices'  => BaseCustomOptionsHolder::getInstance('ExaminationRepprovedType')->getOptionsWithout($options_without, true)
    )));
    
    $this->setValidator('examination_type', new sfValidatorChoice(array(
        'choices' => BaseCustomOptionsHolder::getInstance('ExaminationRepprovedType')->getKeys())
    ));

  }
  
  private function getWithoutExaminationTypes()
  {
    $school_year = SchoolYearPeer::retrieveByPK(sfContext::getInstance()->getUser()->getReferenceFor("schoolyear"));
    $c = new Criteria();
    $c->add(ExaminationRepprovedPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(ExaminationRepprovedSubjectPeer::EXAMINATION_REPPROVED_ID, ExaminationRepprovedPeer::ID);
    $c->add(ExaminationRepprovedSubjectPeer::IS_CLOSED, false);
    $c->clearSelectColumns();
    $c->addSelectColumn(ExaminationRepprovedPeer::EXAMINATION_TYPE);
    $c->setDistinct();
    $stmt = StudentPeer::doSelectStmt($c);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }
}