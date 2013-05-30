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

require_once dirname(__FILE__).'/../lib/head_personalGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/head_personalGeneratorHelper.class.php';

/**
 * head_personal actions.
 *
 * @package    sistema de alumnos
 * @subpackage head_personal
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class head_personalActions extends autoHead_personalActions
{
  /**
   * This action (de)activates person
   *
   * @param sfWebRequest $request
   */
  public function executePersonActivation(sfWebRequest $request)
  {
    $this->related_person = $this->getRoute()->getObject();
    $this->related_person->getPerson()->setIsActive(!$this->related_person->getPersonIsActive());
    $this->related_person->save();
    $this->getUser()->setFlash('info','The item was updated successfully.');
    $this->redirect('@head_personal');
  }

  public function executePreceptors(sfWebRequest $request)
  {
    $this->head_personal = $this->getRoute()->getObject();
    $this->form = new HeadPersonalPersonalManyForm($this->head_personal);
  }
  
  public function executeUpdatePreceptors(sfWebRequest $request)
  {
    $this->head_personal = PersonalPeer::retrieveByPk($request->getParameter('head_personal_personal[id]'));

    if (null === $this->head_personal)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar un jefe de preceptores para configurar sus preceptores');

      $this->redirect('@head_personal');
    }

    $this->form = new HeadPersonalPersonalManyForm($this->head_personal);
    
    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los preceptores seleccionados han sido correctamente asignado al jefe de preceptores.');
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    $this->setTemplate('preceptors');
  }


}