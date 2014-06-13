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

require_once dirname(__FILE__).'/../lib/course_subject_student_examinationGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/course_subject_student_examinationGeneratorHelper.class.php';

/**
 * course_subject_student_examination actions.
 *
 * @package    sistema de alumnos
 * @subpackage course_subject_student_examination
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class course_subject_student_examinationActions extends autoCourse_subject_student_examinationActions
{
  /**
   * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if ($this->getUser()->getAttribute('is_manual_examination_subject', false))
    {
      if (!$this->getUser()->getReferenceFor('manual_examination_subject'))
      {
        $this->getUser()->setFlash('warning', 'Debe seleccionar una mesa para ver sus alumnos');
        $this->redirect("@manual_examination_subject");
      }
    }
    else
    {
      if (!$this->getUser()->getReferenceFor('examination_subject'))
      {
        $this->getUser()->setFlash('warning', 'Debe seleccionar una mesa para ver sus alumnos');
        $this->redirect("@examination_subject");
      }
    }

    parent::preExecute();
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->redirect('@course_subject_student_examination');
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->redirect('@course_subject_student_examination');
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->redirect('@course_subject_student_examination');
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->redirect('@course_subject_student_examination');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->redirect('@course_subject_student_examination');
  }

  public function executeBack(sfWebRequest $request)
  {
    if ($this->getUser()->getAttribute('is_manual_examination_subject', false))
    {
      $this->getUser()->getAttributeHolder()->remove('is_manual_examination_subject');
      $this->redirect("@manual_examination_subject");
    }
    else
    {
      $this->redirect("@examination_subject");
    }
  }
}