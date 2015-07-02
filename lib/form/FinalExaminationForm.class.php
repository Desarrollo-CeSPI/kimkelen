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
 * FinalExamination form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class FinalExaminationForm extends BaseFinalExaminationForm
{
  public function configure()
  {
    unset($this['created_at']);
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
  }
}