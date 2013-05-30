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

require_once dirname(__FILE__).'/../lib/student_freeGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/student_freeGeneratorHelper.class.php';

/**
 * student_free actions.
 *
 * @package    sistema de alumnos
 * @subpackage student_free
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class student_freeActions extends autoStudent_freeActions
{
  /**
   * redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {        

    if (!$this->getUser()->getReferenceFor('student') && !$this->getUser()->getAttribute('student_id'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un alumno para poder administrar su estado libre.');
      $this->redirect('@student');
    }

    $this->student = StudentPeer::retrieveByPK($this->getUser()->getReferenceFor('student'));

    if (is_null($this->student))
    {
      $this->student = StudentPeer::retrieveByPK($this->getUser()->getAttribute('student_id'));      
    }

    if ( is_null($this->student))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un alumno para poder administrar su estado libre.');
      $this->redirect('@student');
    }

    parent::preExecute();
  }

  public function executeBack($request)
  {
    $this->redirect('@student');
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->student_free = new StudentFree();
    $this->student_free->setStudentId($this->student->getId());
    $this->form = $this->configuration->getForm($this->student_free);
    $this->form->setDefault('student_id', $this->student->getId());
  }

  public function executeCreate(sfWebRequest $request)
  {    
    $this->student_free = new StudentFree();
    $this->student_free->setStudentId($this->student->getId());    
    $this->form = $this->configuration->getForm($this->student_free);
    $this->form->setDefault('student_id', $this->student->getId());

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeReincorporate(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);
    $this->forward('@student_reincorporation');
  }

}