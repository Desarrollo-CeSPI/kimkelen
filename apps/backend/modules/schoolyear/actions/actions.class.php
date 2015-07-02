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

require_once dirname(__FILE__).'/../lib/schoolyearGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/schoolyearGeneratorHelper.class.php';

/**
 * schoolyear actions.
 *
 * @package    conservatorio
 * @subpackage schoolyear
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class schoolyearActions extends autoSchoolyearActions
{
  public function preExecute()
  {
    parent::preExecute();

    $not_allowed = array("index", "new", "create");

    if (in_array($this->getActionName(), $not_allowed))
    {
      $this->getUser()->setReferenceFor($this, null);
    }
    else
    {
      $this->getUser()->setReferenceFor($this);
    }
  }

  public function executeChangeState(sfWebRequest $request)
  {
    $school_year = $this->getRoute()->getObject();
    $school_year->active();
    $this->getUser()->setFlash('notice',"La vigencia del año lectivo ha sido modificada correctamente.");
    $this->redirect('@school_year');
  }

  public function executeSchoolYearCareers(sfWebRequest $request)
  {
    $school_year = $this->getRoute()->getObject();
    $this->getUser()->setReferenceFor($this);
    $this->redirect('@career_school_year');
  }

  public function executeRegisteredStudents(sfWebRequest $request)
  {
    $reference_array = array(
      "peer" => "SchoolYearStudentPeer",
      "fk" => "SCHOOL_YEAR_ID",
      "object_id" => $this->getRoute()->getObject()->getId(),
      "back_to" => "@school_year",
      "title" => "School year student list",
    );
    $this->getUser()->setReferenceFor($this, $reference_array, "shared_student");

    $this->redirect("@shared_student");
  }

  public function executeExaminations(sfWebRequest $request)
  {
    $school_year = $this->getRoute()->getObject();
    $this->getUser()->setReferenceFor($this);

    $this->redirect('@examination');
  }

  public function executeExaminationRepproved(sfWebRequest $request)
  {
    $school_year = $this->getRoute()->getObject();
    $this->getUser()->setReferenceFor($this);

    $this->redirect('@examination_repproved');
  }

  public function executeFinalExamination(sfWebRequest $request)
  {
    $school_year = $this->getRoute()->getObject();
    $this->getUser()->setReferenceFor($this);

    $this->redirect('@final_examination');
  }

  public function executeCloseSchoolYear(sfWebRequest $request)
  {
    $school_year = $this->getRoute()->getObject();
    ini_set('max_execution_time', 0);
    $school_year->close();

    $this->getUser()->setFlash('notice', 'The school year has been closed succesfully.');
    $this->redirect('@school_year');
  }

  public function executeManualExaminations(sfWebRequest $request)
  {
    $school_year = $this->getRoute()->getObject();
    $this->getUser()->setReferenceFor($this);

    $this->redirect('@manual_examination');
  }
}