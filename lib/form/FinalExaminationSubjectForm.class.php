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
 * FinalExaminationSubject form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class FinalExaminationSubjectForm extends BaseFinalExaminationSubjectForm
{
  public function configure()
  {
    unset($this['is_closed'], $this['career_subject_id'], $this['created_at']);

    $this->setWidget("final_examination_id", new sfWidgetFormInputHidden());

    $final_examination = $this->getObject()->getFinalExamination();

    $this->getWidget('subject_id')->setOption('peer_method', "doSelectOrdered");
    $this->getWidget('subject_id')->setOption('add_empty', true);

    $this->getWidget("final_examination_subject_teacher_list")->setOption("multiple", true);
    $this->getWidget("final_examination_subject_teacher_list")->setOption("peer_method", 'doSelectActive');
    $this->getWidget("final_examination_subject_teacher_list")->setOption("renderer_class", "csWidgetFormSelectDoubleList");
    $this->getWidget("final_examination_subject_teacher_list")->setLabel('Teachers');

    $this->getWidgetSchema()->moveField('final_examination_subject_teacher_list', 'after', "subject_id");
  }   

}