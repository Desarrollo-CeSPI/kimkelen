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
 * ShowSubjectConfiguration form.
 *
 * @package    conservatorio
 * @subpackage form.custom
 */
class ShowSubjectConfigurationForm extends SubjectConfigurationForm
{
  public function configure()
  {
    parent::configure();
    $subject_configuration = $this->getObject();

    unset(
      $this['course_type'],
      $this['course_marks'],
      $this['course_minimun_mark'],
      $this['attendance_type']
//      $this['max_disciplinary_sanctions']
    );

    $this->setWidget('course_marks_show', new mtWidgetFormPlain(array('object' => $subject_configuration, 'method' => 'getCourseMarks')));
    $this->setWidget('course_minimun_mark_show', new mtWidgetFormPlain(array('object' => $subject_configuration, 'method' => 'getCourseMinimunMark')));
    $this->setWidget('max_previous_show', new mtWidgetFormPlain(array('object' => $subject_configuration, 'method' => 'getMaxPrevious')));
    $this->setWidget('attendance_type_show', new mtWidgetFormPlain(array('object' => $subject_configuration, 'method' => 'getAttendanceTypeString')));
//    $this->setWidget('max_disciplinary_sanctions_show', new mtWidgetFormPlain(array('object' => $subject_configuration, 'method' => 'getMaxDisciplinarySanctions')));

    $this->getWidgetSchema()->moveField('course_marks_show', 'before' , 'course_examination_count');
    $this->getWidgetSchema()->moveField('course_minimun_mark_show', 'before' , 'course_examination_count');
    $this->getWidgetSchema()->moveField('max_previous_show', 'before' , 'course_examination_count');
    $this->getWidgetSchema()->moveField('attendance_type_show', 'before' , 'course_examination_count');
//    $this->getWidgetSchema()->moveField('max_disciplinary_sanctions_show', 'before' , 'course_examination_count');

    $this->getWidgetSchema()->setLabels(array(
      'course_marks_show' => 'Cantidad de Notas',
      'course_minimun_mark_show'=>'Nota mínima de aprobacion',
      'max_disciplinary_sanctions' => 'Cantidad máxima de sanciones',
      'max_previous_show' =>  'Cantidad máxima de previas',
      'attendance_type_show' => 'Tipo de asistencia'
    ));
  }
}