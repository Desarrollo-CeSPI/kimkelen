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
 * Career form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class CareerForm extends BaseCareerForm
{
  
  public function configure()
  {
    unset(
      $this['created_at']
    );

    $max = sfConfig::get('app_career_max_quantity_years', 10);
    $min = $this->getObject()->getMaxYearSubject();

    //Validator
    $this->validatorSchema['quantity_years'] = new sfValidatorInteger(array('min' => $min, 'max' => $max), array(
      'invalid' => 'Debe ingresar un número',
      'min' => 'La carrera debe tener al menos %min% años de duración',
      'max' => 'La carrera no puede tener más de %max% años de duración'
    ));

    $this->validatorSchema->setPostValidator(new sfValidatorPropelUnique(array(
      'model' => 'Career',
      'column' => 'career_name'
    ), array(
      'invalid' => 'Ya existe una carrera con éste nombre.'
    )));

    if (!$this->getObject()->isNew() )
    {
      $this->setWidget('file_number_sequence', new sfWidgetFormReadOnly(array("plain" => false)));
    }

  }

 
}