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
 * AbsenceType form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class AbsenceTypeForm extends BaseAbsenceTypeForm
{

  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
    $choices = array('0' => __('Por dia'), '1' => __('Por materia'));
    $this->setWidget('method', new sfWidgetFormChoice(array('choices' => $choices)));
    $this->getWidget('method')->setLabel('Tipo de asistencia');
    $this->setValidator('method', new sfValidatorChoice(array('choices' => array(0, 1))));

    $faltas = array(
        "1" => '1',
        "0.5" => '1/2',
        "0.33" => '1/3',
        "0.25" => '1/4',
        "0.2" => '1/5',
        "0.16" => '1/6',
        "0.14" => '1/7',
        "0.12" => '1/8',
        "0.11" => '1/9',
        "1.5" => '1 y 1/2',
        "0" => '0',
    );
    $this->setWidget('value', new sfWidgetFormChoice(array(
        'choices' => $faltas,
    )));

    $this->setValidator('value', new sfValidatorChoice(array(
        'choices' => array_keys($faltas)
    )));
    $this->setDefault('value', "0");
  }

}