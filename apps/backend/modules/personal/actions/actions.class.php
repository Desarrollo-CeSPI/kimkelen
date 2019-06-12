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

require_once dirname(__FILE__).'/../lib/personalGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/personalGeneratorHelper.class.php';

/**
 * personal actions.
 *
 * @package    conservatorio
 * @subpackage personal
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class personalActions extends autoPersonalActions
{
    /**
   * This action (de)activates person
   *
   * @param sfWebRequest $request
   */
  public function executePersonActivation(sfWebRequest $request)
  {
    $this->related_person = $this->getRoute()->getObject();
    $this->related_person->getPerson()->setIsActive(!$this->related_person->getPersonIsActive());
    $this->related_person->save();
    $this->getUser()->setFlash('info','The item was updated successfully.');
    $this->redirect('@personal');
  }

  public function executeLicenses(sfWebRequest $request)
  {
    $this->getUser()->removeReferenceFor('teacher');
    $this->getUser()->setReferenceFor($this);
    $this->redirect('license');
  }

  public function executeAggregateAsTeacher(sfWebRequest $request)
  {
    $this->personal = $this->getRoute()->getObject();
    $this->personal->createTeacher();
    $this->getUser()->setFlash('info','The teacher has been created succesfuly.');
    $this->redirect('@personal');
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    $this->personal = $this->getRoute()->getObject();
    
    $guard_user = $this->personal->getPerson()->getSfGuardUser();
    if (!is_null($guard_user))
    {
      $personal_group = BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::PERSONAL);
       
      $group = sfGuardGroupPeer::retrieveByName($personal_group); 

      sfGuardUserGroupPeer::deleteByUserAndGroup($guard_user, $group);
        
    }
    //delete all courses.
    foreach ($this->personal->getDivisionPreceptors() as $division_preceptor) {
        $division_preceptor->delete();
    }
    foreach ($this->personal->getCoursePreceptors() as $course_preceptor) {
        $course_preceptor->delete();
    }
    parent::executeDelete($request);
  }
  
  public function executeAggregateAsTutor(sfWebRequest $request)
  {
    $this->preceptor = PersonalPeer::retrieveByPK($request->getParameter('id'));
    $tutor = new Tutor();
    $tutor->setPerson($this->preceptor->getPerson());
    $this->form = new TutorCustomForm($tutor);

    if ($request->isMethod("post"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash("notice", "The tutor has been created succesfuly.");
        $this->redirect("@personal");
      }
    } 
  }
}