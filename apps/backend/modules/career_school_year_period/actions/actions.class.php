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

require_once dirname(__FILE__).'/../lib/career_school_year_periodGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/career_school_year_periodGeneratorHelper.class.php';

/**
 * career_school_year_period actions.
 *
 * @package    sistema de alumnos
 * @subpackage career_school_year_period
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class career_school_year_periodActions extends autoCareer_school_year_periodActions
{
  /**
   * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('career_school_year'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera de un año lectivo para poder administrar sus periodos.');
      $this->redirect('@career_school_year');
    }
    $this->career_school_year = CareerSchoolYearPeer::retrieveByPK($this->getUser()->getReferenceFor('career_school_year'));
    if ( is_null($this->career_school_year))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera de un año lectivo para poder administrar sus periodos.');
      $this->redirect('@career_school_year');
    }

    parent::preExecute();

  }

  public function executeBack(sfWebRequest $request)
  {
    $this->redirect('@career_school_year');
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = $this->configuration->getForm();
    $this->career_school_year_period = $this->form->getObject();
    $this->form->setDefault('career_school_year_id', $this->getUser()->getReferenceFor('career_school_year'));
  }

  public function addSortCriteria($criteria)
  {
    parent::addSortCriteria($criteria);

    $criteria->addAscendingOrderByColumn(CareerSchoolYearPeriodPeer::COURSE_TYPE);
    $criteria->addAscendingOrderByColumn(CareerSchoolYearPeriodPeer::START_AT);
  }

  public function executeClose(sfWebRequest $request)
  {
    $career_school_year_period = $this->getRoute()->getObject();
    $career_school_year_period->close();

    $this->getUser()->setFlash("notice", "El periodo fue cerrado existosamente.");
    $this->redirect("@career_school_year_period");
  }

  public function executeOpen(sfWebRequest $request)
  {
    $career_school_year_period = $this->getRoute()->getObject();
    $career_school_year_period->open();

    $this->getUser()->setFlash("notice", "El periodo fue abierto exitosamente.");
    $this->redirect("@career_school_year_period");
  }
}