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

require_once dirname(__FILE__).'/../lib/career_subject_optionGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/career_subject_optionGeneratorHelper.class.php';

/**
 * career_subject_option actions.
 *
 * @package    sistema de alumnos
 * @subpackage career_subject_option
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class career_subject_optionActions extends autoCareer_subject_optionActions
{
  /**
   * redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('career'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera para poder administrar las opciones de sus materias.');
      $this->redirect('@career');
    }
    $this->career = CareerPeer::retrieveByPK($this->getUser()->getReferenceFor('career'));
    if ( is_null($this->career))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera para poder administrar las opciones de sus materias.');
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


}