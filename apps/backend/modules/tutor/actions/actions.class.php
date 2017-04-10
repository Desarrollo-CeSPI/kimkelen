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

require_once dirname(__FILE__) . '/../lib/tutorGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/tutorGeneratorHelper.class.php';

/**
 * tutor actions.
 *
 * @package    sistema de alumnos
 * @subpackage tutor
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class tutorActions extends autoTutorActions
{

  public function executeIndexByStudent(sfWebRequest $request)
  {
    parent::executeIndex($request);

    $student_id = $request->getParameter('id');
    $this->pager = $this->getPager();
    $c = new Criteria();
    $c->addjoin(StudentTutorPeer::TUTOR_ID, TutorPeer::ID);
    $c->add(StudentTutorPeer::STUDENT_ID, $student_id);

    $this->pager->SetCriteria($c);

    $this->pager->init();

    $this->setTemplate('index');

  }

  public function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);

    if ($this->getUser()->hasAttribute('previous_module_was_student') && $this->getUser()->getReferenceFor('student'))
    {
      $this->form->setDefault('student_list', array($this->getUser()->getReferenceFor('student')));
    }

  }
  
  public function executeDeactivate(sfWebRequest $request)
  {
     $this->tutor = $this->getRoute()->getObject();
     $this->tutor->getPerson()->setIsActive(false);
     $this->tutor->save();
     $this->getUser()->setFlash('info','The item was updated successfully.');
     $this->redirect('@tutor');
  }
  
  public function executeActivate(sfWebRequest $request)
  {
     $this->tutor = $this->getRoute()->getObject();
     $this->tutor->getPerson()->setIsActive(true);
     $this->tutor->save();
     $this->getUser()->setFlash('info','The item was updated successfully.');
     $this->redirect('@tutor');
  }

}