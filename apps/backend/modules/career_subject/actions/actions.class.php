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
require_once dirname(__FILE__).'/../lib/career_subjectGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/career_subjectGeneratorHelper.class.php';

/**
 * careersubject actions.
 *
 * @package    alumnos
 * @subpackage careersubject
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */

class career_subjectActions extends autoCareer_subjectActions
{
  /**
   * redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('career'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera para poder administrar sus materias.');
      $this->redirect('@career');
    }
    $this->career = CareerPeer::retrieveByPK($this->getUser()->getReferenceFor('career'));
    if ( is_null($this->career))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera para poder administrar sus materias.');
      $this->redirect('@career');
    }

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
      $pager->setParameter('career',$this->career);
      return $pager;
  }


  public function executeBack($request)
  {
    $this->redirect('@career');
  }

  public function executeEditCorrelatives($request)
  {
    $this->career_subject = $this->getRoute()->getObject();
    $correlative = new Correlative();
    $correlative->setCareerSubjectId($this->career_subject->getId());
    $this->form = new CorrelativeForm($correlative);
  }

  public function executeUpdateCorrelatives(sfWebRequest $request)
  {
    $this->career_subject = CareerSubjectPeer::retrieveByPK($request->getParameter('id'));

    if (null === $this->career_subject)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una materia para editar sus correlativas');

      $this->redirect('@career_subject');
    }
    $correlative = new Correlative();
    $correlative->setCareerSubjectId($this->career_subject->getId());

    $this->form = new CorrelativeForm($correlative);

    $this->processForm($request, $this->form);

    $this->setTemplate('editCorrelatives');
  }

  public function getProcessFormNotice($new)
  {
    if ($this->getActionName() == 'updateCorrelatives')
    {
      return 'Las correlativas para la materia seleccionada fueron establecidas correctamente';
    }
    return parent::getProcessFormNotice($new);
  }

  public function setProcessFormErrorFlash()
  {
    if ($this->getActionName() == 'updateCorrelatives')
    {
      $message = 'Las correlativas no pudieron ser establecidas debido a algunos errores.';
      $this->getUser()->setFlash('error', $message, false);
    }
    else
    {
      parent::setProcessFormErrorFlash();
    }
  }

  public function executeAddToCurrentCareerSchoolYear(sfWebRequest $request)
  {
    $this->career_subject = $this->getRoute()->getObject();
    try
    {
      $this->career_subject->addToCurrentCareerSchoolYear();
      $this->getUser()->setFlash('notice', 'La materia fue agregada de forma exitosa.');
    }
    catch (Exception $exc)
    {
      $this->getUser()->setFlash('error', 'La materia no puede ser agregada al año lectivo actual.');
    }
    $this->redirect('@career_subject');
  }



}