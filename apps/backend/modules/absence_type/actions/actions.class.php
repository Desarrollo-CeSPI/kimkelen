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

require_once dirname(__FILE__) . '/../lib/absence_typeGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/absence_typeGeneratorHelper.class.php';

/**
 * absence_type actions.
 *
 * @package    sistema de alumnos
 * @subpackage absence_type
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class absence_typeActions extends autoAbsence_typeActions
{

  public function executeIncrementOrder(sfWebRequest $request)
  {
    $absence_type = $this->getRoute()->getObject();
    $absence_type->incrementOrder();
    $absence_type->save();
    $this->getUser()->setFlash('notice','El orden se incremento con exito');
    $this->redirect('absence_type/index');
  }

  public function executeDecrementOrder(sfWebRequest $request)
  {
    $absence_type = $this->getRoute()->getObject();
    $absence_type->decrementOrder();
    $absence_type->save();
    $this->getUser()->setFlash('notice','El orden se decremento con exito');
    $this->redirect('absence_type/index');

  }

}