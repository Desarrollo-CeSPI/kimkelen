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

require_once dirname(__FILE__).'/../lib/teacherGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/teacherGeneratorHelper.class.php';

/**
 * teacher actions.
 *
 * @package    conservatorio
 * @subpackage teacher
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class teacherActions extends autoTeacherActions
{
  /**
   * This action (de)activates person and relative guard_user to that person
   *
   * @param sfWebRequest $request
   */
  public function executePersonActivation(sfWebRequest $request)
  {
    $this->related_person = $this->getRoute()->getObject();
    $this->related_person->getPerson()->setIsActive(!$this->related_person->getPersonIsActive());
    $this->related_person->save();
    $this->related_person->getPerson()->changeGuardUserActivation();
    $this->getUser()->setFlash('info','The item was updated successfully.');
    $this->redirect('@teacher');
  }

  public function executeCourses(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);
    $this->redirect('shared_course_subject');
  }

  public function executeShowCalendar(sfWebRequest $request)
  {
    $this->teacher = $this->getRoute()->getObject();
    $this->events = json_encode($this->teacher->getWeekCalendar());
  }

  public function executeLicenses(sfWebRequest $request)
  {
    $this->getUser()->removeReferenceFor('personal');
    $this->getUser()->setReferenceFor($this);
    $this->redirect('license');
  }

  public function executeAggregateAsPreceptor(sfWebRequest $request)
  {
    $this->teacher = $this->getRoute()->getObject();
    $this->teacher->createPreceptor();
    $this->getUser()->setFlash('info','The preceptor has been created succesfuly.');
    $this->redirect('@teacher');
  }

}