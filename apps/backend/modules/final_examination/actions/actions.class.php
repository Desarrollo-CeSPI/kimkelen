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

require_once dirname(__FILE__).'/../lib/final_examinationGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/final_examinationGeneratorHelper.class.php';

/**
 * final_examination actions.
 *
 * @package    sistema de alumnos
 * @subpackage final_examination
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class final_examinationActions extends autoFinal_examinationActions
{
  /**
   * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('schoolyear'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un año lectivo para poder administrar las mesas de final.');
      $this->redirect('@schoolyear');
    }

    $this->school_year = SchoolYearPeer::retrieveByPK($this->getUser()->getReferenceFor('schoolyear'));

    if (is_null($this->school_year))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un año lectivo para poder administrar las mesas de final.');
      $this->redirect('@schoolyear');
    }

    parent::preExecute();
  }

  public function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);

    $this->form->setDefault("school_year_id", $this->school_year->getId());
  }

  public function executeSubjects(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);

    $this->redirect("@final_examination_subject");
  }

  public function executeBack(sfWebRequest $request)
  {
    $this->redirect("@school_year");
  }
}