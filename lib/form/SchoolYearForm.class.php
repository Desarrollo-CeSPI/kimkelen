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
 * SchoolYear form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class SchoolYearForm extends BaseSchoolYearForm
{
  public function configure()
  {
    unset(
      $this['student_school_year_list']
    );

    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','Url'));

    //widgets
    $this->widgetSchema['year'] = new sfWidgetFormInput(array());

    $date = SchoolYearPeer::suggestYear();
    $this->widgetSchema['year']->setDefault($date);


    //validators
    $this->validatorSchema['year'] = new sfValidatorInteger(array(
      'max' => 9999,
      'min' => 1900,
    ));

    $this->validatorSchema['year']->addMessage('max', '"%value%" debe ser menor a %max%.');
    $this->validatorSchema['year']->addMessage('min', '"%value%" debe ser mayor a  %min%.');
    $this->validatorSchema['year']->addMessage('invalid', 'Debe ingresar un número.');
    $this->validatorSchema->setPostValidator(new sfValidatorPropelUnique(array(
      'model'   => 'SchoolYear',
      'column'  => 'year'),
      array('invalid' => 'El año lectivo ya existe en el sistema.'))
      );    
  }
}