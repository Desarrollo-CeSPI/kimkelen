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
 * SubjectConfiguration form for BBA.
 * @subpackage form
 * @author     pmacadden
 */
class BbaCareerSubjectSchoolYearConfigurationForm extends BbaSubjectConfigurationForm
{
  public function configure()
  {
    parent::configure();

    //$this->widgetSchema->setHelp('max_absence','Se define la cantidad de horas que el alumno puede faltar. Solo en el caso de ser asistencia por materia.');

    unset(
      $this["max_previous"],
      $this["max_disciplinary_sanctions"]
    );
  }

  public function doSave($con = null)
  {
    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $this->updateObject();
    $this->object->setCourseMarks(3);
    if ($this->object->getCourseType() == CourseType::BIMESTER){
      $this->object->setEvaluationMethod(EvaluationMethod::FINAL_PROM);
    }
    else
    {
      $this->object->setEvaluationMethod(BaseCustomOptionsHolder::getInstance('CourseType')->getEvaluationMethodFor($this->object->getCourseType()));
    }

    //$this->object->setAttendanceType($this->getValues('attendance_type'));
    $this->object->save($con);
    // embedded forms
    $this->saveEmbeddedForms($con);
  }
}