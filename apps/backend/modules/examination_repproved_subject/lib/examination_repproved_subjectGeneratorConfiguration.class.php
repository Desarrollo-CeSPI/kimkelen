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

/**
 * examination_repproved_subject module configuration.
 *
 * @package    sistema de alumnos
 * @subpackage examination_repproved_subject
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 12474 2008-10-31 10:41:27Z fabien $
 */
class examination_repproved_subjectGeneratorConfiguration extends BaseExamination_repproved_subjectGeneratorConfiguration
{
  public function getForm($object = null)
  {
    $form = SchoolBehaviourFactory::getInstance()->getFormFactory()->getStudentExaminationRepprovedSubjectForm();
    return new $form($object);
  }
}