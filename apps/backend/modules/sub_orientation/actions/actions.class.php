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

require_once dirname(__FILE__).'/../lib/sub_orientationGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/sub_orientationGeneratorHelper.class.php';

/**
 * sub_orientation actions.
 *
 * @package    sistema de alumnos
 * @subpackage sub_orientation
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class sub_orientationActions extends autoSub_orientationActions
{
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('orientation'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una orientación para poder administrar sus sub orientaciones.');
      $this->redirect('@orientation');
    }
    
    $this->orientation = OrientationPeer::retrieveByPK($this->getUser()->getReferenceFor('orientation'));
    
    if (is_null($this->orientation))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una orientación para poder administrar sus sub orientaciones.');
      $this->redirect('@orientation');
    }
    
    parent::preExecute();
  }
  
  public function executeBack($request)
  {
    $this->redirect('@orientation');
  }
  
  public function executeNew(sfWebRequest $request)
  {
    parent::executeNew($request);
    
    $this->form->setDefault("orientation_id", $this->orientation->getId());
  }
}