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

require_once dirname(__FILE__).'/../lib/shared_course_subjectGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/shared_course_subjectGeneratorHelper.class.php';

/**
 * shared_course_subject actions.
 *
 * @package    sistema de alumnos
 * @subpackage shared_course_subject
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class shared_course_subjectActions extends autoShared_course_subjectActions
{
  /**
   * redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {    
    if (!$this->getUser()->getReferenceFor('teacher'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un profesor para poder ver sus materias.');      
      $this->redirect('@teacher');
    }
    $this->teacher = TeacherPeer::retrieveByPK($this->getUser()->getReferenceFor('teacher'));
    if ( is_null($this->teacher))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar un profesor para poder ver sus materias.');
      $this->redirect('@teacher');
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
    $pager->setParameter('teacher',$this->teacher);
    return $pager;
  }
}