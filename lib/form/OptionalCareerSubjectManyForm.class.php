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
 * DivisionPreceptorMany
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class OptionalCareerSubjectManyForm extends sfFormPropel
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $this->loadHelpers(array('Tag', 'Asset'));
    //WIDGETS
    $this->setWidget('id', new sfWidgetFormInputHidden());
    $criteria = CareerSubjectSchoolYearPeer::getAvailableChoicesCriteria($this->getObject(), $exclude_related = false, $exclude_repetead = false );

    $this->setWidget('option_career_subject_list', new sfWidgetFormPropelChoice(array(
      'model'     => 'CareerSubjectSchoolYear',
      'multiple'  => true,
      'criteria'  => $criteria,
      'renderer_class'  => 'csWidgetFormSelectDoubleList'
      )));

    $this->getWidget('option_career_subject_list')->setLabel('Opciones');
    $this->widgetSchema->setNameFormat('optional_career_subject[%s]');

    //VALIDATORS
    $this->setValidator('id', new sfValidatorPropelChoice(array('model' => 'CareerSubjectSchoolYear')));
    $this->setValidator('option_career_subject_list', new sfValidatorPropelChoice(array(
      'model'    => 'CareerSubjectSchoolYear',
      'multiple' => true,
      'required' => false,
      'criteria' => $criteria
    )));
  }

  public function getModelName()
  {
    return 'CareerSubjectSchoolYear';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();


    if (isset($this->widgetSchema['option_career_subject_list']))
    {
      $values = array();
      foreach ($this->object->getChoices() as $option_career_subject)
      {
        $values[] = $option_career_subject->getChoiceCareerSubjectSchoolYearId();
      }

      $this->setDefault('option_career_subject_list', $values);
    }
  }

  protected function loadHelpers(array $helpers)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers($helpers);
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->saveOptionCareerSubjectList($con);
  }

  public function saveOptionCareerSubjectList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['option_career_subject_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    foreach ($this->getObject()->getChoices() as $option_career_subject)
    {
      $option_career_subject->delete($con);
    }

    $values = $this->getValue('option_career_subject_list');
    if (is_array($values))
    {
      $con->beginTransaction();
      try
      {

        foreach ($values as $value)
        {
          $option_career_subject = new OptionalCareerSubject();
          $option_career_subject->setCareerSubjectSchoolYearId($this->getObject()->getId());
          $option_career_subject->setChoiceCareerSubjectSchoolYearId($value);
          $option_career_subject->save();
        }
        $con->commit();
      }
      catch (Exception $e)
      {
        $con->rollBack();
      }
    }
  }


}