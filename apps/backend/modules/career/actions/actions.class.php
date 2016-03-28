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
require_once dirname(__FILE__).'/../lib/careerGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/careerGeneratorHelper.class.php';
/**
 * career actions.
 *
 * @package    alumnos
 * @subpackage career
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */

class careerActions extends autoCareerActions
{
  /**
   * Executes parent's preExecute method and sets reference for career.
   * This is very important for the context menu.
   */
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

  public function executeStudents(sfWebRequest $request)
  {
    $reference_array = array(
      "peer" => "CareerStudentPeer",
      "fk" => "CAREER_ID",
      "object_id" => $this->getRoute()->getObject()->getId(),
      "back_to" => "@career",
      // esto es para agregar un filtro
      //"filter_class" => "StudentFormFilter"
      "title" => "Career student list",
      "back_to_label" => "Return to career list"
    );
    $this->getUser()->setReferenceFor($this, $reference_array, "shared_student");

    $this->redirect('@shared_student');
  }

  public function executeSubjects()
  {
    $this->redirect('@career_subject');
  }

  public function executeSubjectOptions()
  {
    $this->redirect('@career_subject_option');
  }

  public function executeCareerView(sfWebRequest $request)
  {
    $this->career = $this->getRoute()->getObject();
    $this->school_year = null;

    $this->module = $this->getModuleName();
  }

  public function executeCopy(sfWebRequest $request)
  {
      set_time_limit(0);
      $this->career = $this->getRoute()->getObject()->getCopy();
      $this->career->setCareerName($this->career->getCareerName().' (copia)');
      $this->career->setPlanName($this->career->getPlanName().' (copia)');
      $this->career->setFileNumberSequence(0);
      $this->career->save();
      $this->getUser()->setFlash('info',"Se copió la carrera ".$this->getRoute()->getObject().' en '.$this->career);
      $this->redirect("@career");
  }
}