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

require_once dirname(__FILE__).'/../lib/final_examination_subjectGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/final_examination_subjectGeneratorHelper.class.php';

/**
 * final_examination_subject actions.
 *
 * @package    sistema de alumnos
 * @subpackage final_examination_subject
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class final_examination_subjectActions extends autoFinal_examination_subjectActions
{
  /**
   * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('final_examination'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una mesa de final para poder administrar sus materias.');
      $this->redirect('@final_examination');
    }

    $this->final_examination = FinalExaminationPeer::retrieveByPK($this->getUser()->getReferenceFor('final_examination'));

    if (is_null($this->final_examination))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una mesa de final para poder administrar sus materias.');
      $this->redirect('@final_examination');
    }

    parent::preExecute();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->final_examination_subject = new FinalExaminationSubject();
    $this->final_examination_subject->setFinalExamination($this->final_examination);

    $this->form = new FinalExaminationSubjectForm($this->final_examination_subject);
    $this->form->setDefault('final_examination_id', $this->final_examination->getId());
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->final_examination_subject = new FinalExaminationSubject();
    $this->final_examination_subject->setFinalExamination($this->final_examination);

    $this->form = new FinalExaminationSubjectForm($this->final_examination_subject);

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeStudents(sfWebRequest $request)
  {
    $this->final_examination_subject = $this->getRoute()->getObject();
    $this->form = new FinalExaminationSubjectStudentsForm($this->final_examination_subject);
    $this->form->setDefault('final_examination_subject_id', $this->final_examination_subject->getId());
  }

  public function executeUpdateStudents(sfWebRequest $request)
  {
    $this->final_examination_subject = FinalExaminationSubjectPeer::retrieveByPk($request->getParameter('final_examination_subject[final_examination_subject_id]'));

    if (null === $this->final_examination_subject)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una materia para inscribir a los estudiantes');

      $this->redirect('@final_examination_subject');
    }

    $this->form = new FinalExaminationSubjectStudentsForm($this->final_examination_subject);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los alumnos seleccionados han sido correctamente inscriptos a la mesa.');
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }

    $this->setTemplate('students');
  }

  public function executeCalifications(sfWebRequest $request)
  {
    $this->final_examination_subject = $this->getRoute()->getObject();
    $this->form = new FinalExaminationSubjectCalificationssForm($this->final_examination_subject);
    $this->form->setDefault('final_examination_subject_id', $this->final_examination_subject->getId());
  }
  
  public function executeUpdateCalifications(sfWebRequest $request)
  {
    $this->final_examination_subject = FinalExaminationSubjectPeer::retrieveByPk($request->getParameter('final_examination_subject[final_examination_subject_id]'));

    if (null === $this->final_examination_subject)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una materia para calificar a los estudiantes');

      $this->redirect('@final_examination_subject');
    }

    $this->form = new FinalExaminationSubjectCalificationssForm($this->final_examination_subject);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash('notice', 'Las calificaciones han sido correctamente guardadas en la mesa.');
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }

    $this->setTemplate('califications');
  }

  public function executeClose(sfWebRequest $request)
  {
    $this->final_examination_subject = $this->getRoute()->getObject();
  }

  public function executeRealClose(sfWebRequest $request)
  {
    $this->final_examination_subject = FinalExaminationSubjectPeer::retrieveByPK($request->getParameter("id"));
    $this->final_examination_subject->close();

    $this->getUser()->setFlash("notice", "La mesa de final fue cerrada con exito.");
    $this->redirect("@final_examination_subject");
  }

  public function executeBack()
  {
    $this->redirect("@final_examination");
  }
}