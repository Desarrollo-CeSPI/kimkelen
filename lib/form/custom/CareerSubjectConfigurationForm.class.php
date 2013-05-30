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
 * SubjectConfiguration form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     pmacadden
 */
class CareerSubjectConfigurationForm extends SubjectConfigurationForm
{
  
  public function configure()
  {
    parent::configure();

    unset(
      $this["course_required"], $this["final_examination_required"], $this['course_examination_count'], $this['max_previous'], $this['max_disciplinary_sanctions'], $this['evaluation_method'], $this['when_disapprove_show_string'], $this['necessary_student_approved_career_subject_to_show_prom_def']
    );
    $this->getWidgetSchema('course_marks')->setHelp('course_marks', 'Si se cambia la cantidad de notas de un curso existente se perderán los valores de estas notas.');
    $this->getWidgetSchema('course_type')->setHelp('course_type', 'Si se cambia el tipo de curso de un curso existente se perderá la configuración del mismo.');

    if ($this->getObject()->isNew())
    {
      $this->getWidget('course_type')->setDefault($this->getObject()->getCourseType());
//      var_dump($this->getWidget('course_type')->getDefault());
    }
    $this->getWidgetSchema('attendance_type')->setHelp('attendance_type', 'Se define el tipo de asistencia que tendrán las materias. Si se cambia de "por materia" a "por día", se perderán los valores de los cursos existentes.');

  }

}