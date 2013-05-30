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

require_once dirname(__FILE__).'/../lib/student_reincorporationGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/student_reincorporationGeneratorHelper.class.php';

/**
 * student_reincorporation actions.
 *
 * @package    sistema de alumnos
 * @subpackage student_reincorporation
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class student_reincorporationActions extends autoStudent_reincorporationActions
{
  /**
   * redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   * 
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('student') && is_null($this->getUser()->getAttribute('student_id')))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un alumno para poder administrar sus reincorporaciones.');
      $this->redirect('@student');
    }
    $this->student = StudentPeer::retrieveByPK($this->getUser()->getReferenceFor('student'));

    if (is_null($this->student))
    {
      $this->student = StudentPeer::retrieveByPK($this->getUser()->getAttribute('student_id'));      
    }

    if (is_null($this->student))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un alumno para poder administrar sus reincorporaciones.');
      $this->redirect('@student');
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
    $pager->setParameter('student',$this->student);
    return $pager;
  }

  public function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);
    
    $this->form->setDefaults(array('student_id' => $this->student->getId()));    
  }

  public function executeBack(sfWebRequest $request)
  {    
    $this->redirect('@student');
  }
}