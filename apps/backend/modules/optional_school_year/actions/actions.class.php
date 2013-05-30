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

require_once dirname(__FILE__).'/../lib/optional_school_yearGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/optional_school_yearGeneratorHelper.class.php';

/**
 * optional_school_year actions.
 *
 * @package    sistema de alumnos
 * @subpackage optional_school_year
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class optional_school_yearActions extends autoOptional_school_yearActions
{

  /**
   * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   * 
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('career_school_year'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera del año lectivo para poder administrar sus opciones.');
      $this->redirect('@career_school_year');
    }
    $this->career_school_year = CareerSchoolYearPeer::retrieveByPK($this->getUser()->getReferenceFor('career_school_year'));
    
    if ( is_null($this->career_school_year))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera del año lectivo para poder administrar sus opciones.');
      $this->redirect('@career_school_year');
    }

    $this->career_school_year->checkCareerSubjectOptionsIntegrity();
    parent::preExecute();

  }

  /**
   * Redefines parent::getPager because we need to add a custom parameter: career
   * used by _list_header partial
   *
   * @return sfPropelPager
   */
  public function getPager()
  {
    /* @var $pager sfPropelPager */
    $pager = parent::getPager();
    $pager->setParameter('career_school_year',$this->career_school_year);
    return $pager;
  }

  public function executeOptional(sfWebRequest $request)
  {
    $this->career_subject_school_year = $this->getRoute()->getObject();
    $this->form = new OptionalCareerSubjectManyForm($this->career_subject_school_year);

  }
  public function executeUpdateOptional(sfWebRequest $request)
  {
    $this->career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByPk($request->getParameter('id'));

    if (null === $this->career_subject_school_year )
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una materia para editar sus opciones');
      $this->redirect('@career_school_year');
    }
    $this->form = new OptionalCareerSubjectManyForm($this->career_subject_school_year);

    $this->processForm($request, $this->form);

    $this->setTemplate('optional');
  }

  public function getProcessFormNotice($new)
  {
    if ($this->getActionName() == 'updateOptional')
    {
      return 'Las opciones para la materia seleccionada fueron establecidas correctamente';
    }
    return parent::getProcessFormNotice();
  }

  public function setProcessFormErrorFlash()
  {
    if ($this->getActionName() == 'updateOptional')
    {
      $message = 'Las opciones no pudieron ser establecidas debido a algunos errores.';
      $this->getUser()->setFlash('error', $message, false);
    }
    else
    {
      parent::setProcessFormErrorFlash();
    }
  }
  
  /** tags! **/
  
  public function executeTags(sfWebRequest $request)
  {
    $this->career_subject_school_year = $this->getRoute()->getObject();

    $this->form = new CareerSubjectSchoolYearTaggableForm($this->career_subject_school_year);
  }
  
  public function executeUpdateTags(sfWebRequest $request)
  {
    $this->career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByPK($request->getParameter('id'));

    if (null === $this->career_school_year)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una carrera para editar sus etiquetas');

      $this->redirect('@career_school_year');
    }

    $this->form = new CareerSubjectSchoolYearTaggableForm($this->career_subject_school_year);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $notice = $this->getProcessFormNotice($this->form->getObject()->isNew());

      $subject_configuration = $this->form->save();

      $this->getUser()->setFlash('notice', $notice);
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    
    $this->setTemplate('tags');
  }
}