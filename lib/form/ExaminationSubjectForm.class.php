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
 * ExaminationSubject form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class ExaminationSubjectForm extends BaseExaminationSubjectForm
{
  public function configure()
  {
    unset(
      $this["examination_id"],
      $this["career_subject_school_year_id"],
      $this["is_closed"]
    );
    
    $this->widgetSchema["examination_subject_teacher_list"]->setOption("multiple", true);
    $this->widgetSchema["examination_subject_teacher_list"]->setOption("peer_method", 'doSelectActive');
    $this->widgetSchema["examination_subject_teacher_list"]->setOption("renderer_class", "csWidgetFormSelectDoubleList");
  }
}