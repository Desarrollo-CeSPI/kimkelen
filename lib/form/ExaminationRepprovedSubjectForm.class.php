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
 * ExaminationRepprovedSubject form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class ExaminationRepprovedSubjectForm extends BaseExaminationRepprovedSubjectForm
{
  public function configure()
  {
    unset($this['is_closed']);
    $this->setWidget("examination_repproved_id", new sfWidgetFormInputHidden());

    $examination_repproved = $this->getObject()->getExaminationRepproved();

    if ($this->getObject()->isNew())
    {
      $criteria = CareerSubjectPeer::retrieveForExaminationRepprovedCriteria($examination_repproved);
      $this->getWidget('career_subject_id')->setOption('criteria', $criteria);
      $this->getValidator('career_subject_id')->setOption('criteria', $criteria);
      $this->getWidget('career_subject_id')->setLabel('Subject');
    }
    else
    {
      //Si es en edicion solo muestro la info, no se puede modificar la materia.
      unset($this['career_subject_id']);
      $this->setWidget('career_subject_wrapper', new mtWidgetFormWrapper(array('content' => $this->getObject()->getCareerSubject())));
      $this->setValidator('career_subject_wrapper', new sfValidatorPass());
      $this->getWidgetSchema()->moveField('career_subject_wrapper', 'before','date');
      $this->getWidget('career_subject_wrapper')->setLabel('Subject');
    }
    
    $this->setWidget('date', new csWidgetFormDateInput());
    $this->setValidator('date', new mtValidatorDateString(array('required' => false)));
    
    $this->getWidget("examination_repproved_subject_teacher_list")->setOption("multiple", true);
    $this->getWidget("examination_repproved_subject_teacher_list")->setOption("peer_method", 'doSelectActive');
    $this->getWidget("examination_repproved_subject_teacher_list")->setOption("renderer_class", "csWidgetFormSelectDoubleList");
  }
}