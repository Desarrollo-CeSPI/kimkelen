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

require_once dirname(__FILE__).'/../lib/occupationGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/occupationGeneratorHelper.class.php';

/**
 * occupation actions.
 *
 * @package    sistema de alumnos
 * @subpackage occupation
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class occupationActions extends autoOccupationActions
{
  public function setDeleteFlash()
  {
    $this->getUser()->setFlash('notice', 'La ocupación fue eliminada correctamente.');
  }

  public function getProcessFormNotice($new)
  {
    return $new ? 'La ocupación fue creada correctamente.' : 'La ocupación fue actualizada correctamente.';
  }

  public function setProcessFormErrorFlash()
  {
    $this->getUser()->setFlash('error', 'La ocupación no fue guardada debido a algunos errores.', false);
  }

  public function setProcessFormSaveAndAddFlash($notice)
  {
    $this->getUser()->setFlash('notice', $notice.' Puede agregar otra más abajo.');
  }
}