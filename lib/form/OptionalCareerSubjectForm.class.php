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
 * OptionalCareerSubject form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class OptionalCareerSubjectForm extends BaseOptionalCareerSubjectForm
{
  public function configure()
  {
    $this->setWidget('career_subject_id', new sfWidgetFormReadOnly(array(
      'plain'          => false,
      'value_callback' => array('CareerSubjectPeer', 'retrieveByPK')
    )));
    
    $this->setWidget('subject_id', new sfWidgetFormPropelChoice(array(
      'model'     => 'Subject',
      'add_empty' => 'Seleccione la opción'
    )));

    $this->setValidator('subject_id', new sfValidatorPropelChoice(array(
      'model' => 'Subject',
      'required' => true
    )));

    //$this->validatorSchema->setOption('allow_extra_fields', true);

    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'validate')
    ),
      array(
        'invalid' => 'La opción que intenta agregar ya existe.'
    )));
  }

  public function validate($validator, $value, $arguments)
  {
    $career_subject = CareerSubjectPeer::retrieveByPK($value['career_subject_id']);

    $c = new Criteria();
    $c->add(CareerSubjectPeer::SUBJECT_ID, $value['subject_id']);
    $c->add(CareerSubjectPeer::CAREER_ID, $career_subject->getCareerId());

    if (CareerSubjectPeer::doSelect($c))
    {
      throw new sfValidatorError($validator, 'invalid');
    }

    return $value;
  }

  protected function doSave($con = null)
  {
    $values = $this->getValues();

    $optional_career_subject = new CareerSubject();
    $optional_career_subject->setSubjectId($values['subject_id']);
    $optional_career_subject->setIsOption(true);
    $career_subject = CareerSubjectPeer::retrieveByPK($values['career_subject_id']);
    $optional_career_subject->setCareerId($career_subject->getCareerId());
    $optional_career_subject->setYear($career_subject->getYear());
    $optional_career_subject->setSubjectConfigurationId($career_subject->getSubjectConfigurationId());
    $optional_career_subject->setCreditHours($career_subject->getCreditHours());
    $this->getObject()->setCareerSubjectRelatedByOptionalCareerSubjectId($optional_career_subject);

    parent::doSave($con);

    $career_subject->setHasOptions(true);
    $career_subject->save($con);
  }
}