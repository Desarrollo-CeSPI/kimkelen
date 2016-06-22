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

require_once dirname(__FILE__) . '/../lib/career_school_yearGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/career_school_yearGeneratorHelper.class.php';

/**
 * career_school_year actions.
 *
 * @package    sistema de alumnos
 * @subpackage career_school_year
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class career_school_yearActions extends autoCareer_school_yearActions
{

  /**
   * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('schoolyear'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un año lectivo para poder administrar sus carreras.');
      $this->redirect('@schoolyear');
    }
    $this->school_year = SchoolYearPeer::retrieveByPK($this->getUser()->getReferenceFor('schoolyear'));
    if (is_null($this->school_year))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un año lectivo para poder administrar sus carreras.');
      $this->redirect('@schoolyear');
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
    $pager->setParameter('school_year', $this->school_year);
    return $pager;

  }

  /**
   * This action creates a career for the school_year selected
   */
  public function executeCreateCareer(sfWebRequest $request)
  {
    $career = CareerPeer::retrieveByPk($request->getParameter('career_id'));
    $this->school_year->createCareerSchoolYear($career);
    $this->getUser()->setFlash('notice', 'Se creo la carrera para el año lectivo');
    $this->redirect('@career_school_year');

  }

  /**
   * This action render ther SubjectConfiguration Form
   */
  public function executeConfiguration(sfWebRequest $request)
  {
    $this->career_school_year = $this->getRoute()->getObject();
    $subject_configuration = $this->career_school_year->getSubjectConfiguration();

    $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCareerSchoolYearConfigurationForm();
    $this->form = new $form_name($subject_configuration);
  }

  /*
   *  This method validates the form, and if its correct its save.
   *
   */

  public function executeUpdateConfiguration(sfWebRequest $request)
  {
    $this->career_school_year = CareerSchoolYearPeer::retrieveByPK($request->getParameter('id'));

    if (null === $this->career_school_year)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una carrera para editar su configuracion');

      $this->redirect('@career_school_year');
    }

    $subject_configuration = $this->career_school_year->getSubjectConfiguration();
    $this->career_school_year->setSubjectConfiguration($subject_configuration);

    $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCareerSchoolYearConfigurationForm();
    $this->form = new $form_name($subject_configuration);

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
    $this->setTemplate('configuration');

  }

  /**
   * This action render ther SubjectConfiguration Form
   */
  public function executeShowConfiguration(sfWebRequest $request)
  {
    $this->career_school_year = $this->getRoute()->getObject();
    $subject_configuration = $this->career_school_year->getSubjectConfiguration();
    $this->form = new ShowSubjectConfigurationForm($subject_configuration);
  }

  /*
   *  This method validates the form, and if its correct its save.
   *
   */
  public function executeUpdateShowConfiguration(sfWebRequest $request)
  {
    $this->career_school_year = CareerSchoolYearPeer::retrieveByPK($request->getParameter('id'));

    if (null === $this->career_school_year)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una carrera para editar su configuracion');

      $this->redirect('@career_school_year');
    }

    $subject_configuration = $this->career_school_year->getSubjectConfiguration();
    $this->career_school_year->setSubjectConfiguration($subject_configuration);

    $this->form = new ShowSubjectConfigurationForm($subject_configuration);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

    if ($this->form->isValid())
    {
      $notice = $this->getProcessFormNotice(false);
      $this->form->save();
      $this->getUser()->setFlash('notice', $notice);
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    $this->setTemplate('showConfiguration');

  }

  /*
   * This method sets the reference and redirects to career_subjects_school_year module.
   */

  public function executeCareerCareerSubjectSchoolYear(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);
    $this->redirect('@career_subject_school_year');

  }

  public function executeOptionals(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);
    $this->redirect('@optional_school_year');

  }

  public function executeCareerView(sfWebRequest $request)
  {
    $this->career_school_year = $this->getRoute()->getObject();

    $this->career = $this->career_school_year->getCareer();
    $this->school_year = $this->career_school_year->getSchoolYear();

    $this->module = $this->getModuleName();

  }

  public function executeClose(sfWebRequest $reuqest)
  {
    ini_set('max_execution_time', 0);
    $this->career_school_year = $this->getRoute()->getObject();
    $this->result = array();
    $this->result = $this->career_school_year->close();
    //Si hay errores muestro los errores
    if (is_array($this->result))
    {
      $this->getUser()->setFlash('error', "Hay errores en algunos alumnos.");
      $this->setTemplate('studentWithErrors');
    }
    //si no  hay errores  la carrera fue cerrada con exito
    else
    {
      $this->getUser()->setFlash('notice', "La carrera fue cerrada con exito.");
      $this->redirect('@career_school_year');
    }

  }

  public function executeCopyConfiguration(sfWebRequest $request)
  {
    $this->career_school_year = $this->getRoute()->getObject();
    $this->career_school_year->copyConfiguration();
    $this->getUser()->setFlash('notice', "La configuración fue copiada con exito");
    $this->redirect('@career_school_year');
  }

  /*
   * This method sets the reference and redirects to career_school_year_period module.
   */

  public function executePeriod(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);
    $this->redirect('@career_school_year_period');
  }

  public function executeMatriculateLastYearStudents(sfWebRequest $request)
  {
    ini_set('max_execution_time', 0);
    $this->career_school_year = $this->getRoute()->getObject();
    $this->career_school_year->matriculateLastYearStudents();
    $this->getUser()->setFlash('notice', "Los alumnos fueron matriculados con exito.");
    $this->redirect('@career_school_year');
  }

  public function executeCreateLastYearDivisions(sfWebRequest $request)
  {
    ini_set('max_execution_time', 0);
    $this->career_school_year = $this->getRoute()->getObject();
    $this->career_school_year->createLastYearDivisions();
    $this->getUser()->setFlash('notice', "Se crearon las divisiones y se anotaron a los alumnos en sus respectivas.");
    $this->redirect('@career_school_year');
  }

  public function executeCreateLastYearCommissions(sfWebRequest $request)
  {
    ini_set('max_execution_time', 0);
    $this->career_school_year = $this->getRoute()->getObject();
    $this->career_school_year->createLastYearCommissions();
    $this->getUser()->setFlash('notice', "Se crearon las comisiones y se anotaron a los alumnos en sus respectivas.");
    $this->redirect('@career_school_year');
  }

  public function executeMatriculateGraduatedFromOtherCareer(sfWebRequest $request)
  {
    $this->career_school_year = $this->getRoute()->getObject();
    $this->form = new MatriculateGraduatedStudentsForm();
   
  }

  public function executeSaveMatriculateGraduatedFromOtherCareer(sfWebRequest $request)
  {
    if ($request->isMethod('POST'))
    {
      $destiny_career_id = $request->getParameter('id');
      if (is_null($destiny_career_id))
      {
        $this->getUser()->setFlash('error', 'Ocurrió un error y no se guardaron los cambios.');

        $this->redirect('@career_school_year');
      }

      $this->form = new MatriculateGraduatedStudentsForm();
      $this->form->setOption('destiny_career_id', $destiny_career_id);
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

      if ($this->form->isValid())
      {
        try
        {
          $this->form->save();
          $this->getUser()->setFlash('notice', 'Los egresados fueron matriculados en la carrera exitosamente.');
        }
        catch (PropelException $e)
        {
          $this->getUser()->setFlash('error', 'Ocurrió un error y no se guardaron los cambios.');
        }
     
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrió un error y no se guardaron los cambios.');
      }
    }

    $this->redirect('@career_school_year');
  }


}