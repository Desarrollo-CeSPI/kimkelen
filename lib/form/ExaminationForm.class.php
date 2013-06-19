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
 * Examination form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class ExaminationForm extends BaseExaminationForm
{
  public function configure()
  {
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

    $school_year = SchoolYearPeer::retrieveByPK(sfContext::getInstance()->getUser()->getReferenceFor("schoolyear"));
    $this->getValidator('examination_number')->setOption('max', $school_year->getMaxCourseExaminationCount());
    $this->getValidator('examination_number')->setMessage('max', 'El número de instancia de mesa no puede ser mayor que %max%');
  }
}