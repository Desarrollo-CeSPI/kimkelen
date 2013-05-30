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
class DivisionPreceptorManyForm extends sfFormPropel
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $this->configureWidgets();
    $this->configureValidators();
  }

  public function configureWidgets()
  {
    $this->loadHelpers(array('Tag', 'Asset'));
    $this->setWidget('id', new sfWidgetFormInputHidden());
    
    $this->setWidget('division_preceptor_list', new sfWidgetFormPropelChoice(array(
      'model'     => 'Personal',
      'peer_method' => 'doSelectActivePreceptor',
      'multiple'  => true,
      "renderer_class"  => "csWidgetFormSelectDoubleList",
      )));

    $this->getWidget('division_preceptor_list')->setLabel('Preceptores');
    $this->widgetSchema->setNameFormat('division[%s]');
  }

  public function configureValidators()
  {
    $this->setValidator('id', new sfValidatorPropelChoice(array('model' => 'Division')));
    $this->setValidator('division_preceptor_list', new sfValidatorPropelChoice(array(
      'model'    => 'Personal',
      'multiple' => true,
      'required' => false,
    )));
  }
  
  public function getModelName()
  {
    return 'Division';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['division_preceptor_list']))
    {
      $values = array();
      foreach ($this->object->getDivisionPreceptors() as $division_preceptor)
      {
        $values[] = $division_preceptor->getPreceptorId();
      }

      $this->setDefault('division_preceptor_list', $values);
    }
  }

  protected function loadHelpers(array $helpers)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers($helpers);
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->saveDivisionPreceptorList($con);
  }

  public function saveDivisionPreceptorList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['division_preceptor_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }
        
    foreach ($this->getObject()->getDivisionPreceptors() as $division_preceptor)
    {
      $division_preceptor->delete($con);
    }

    $values = $this->getValue('division_preceptor_list');
    if (is_array($values))
    {
      $con->beginTransaction();
      try
      {

        foreach ($values as $value)
        {
          $division_preceptor = new DivisionPreceptor();
          $division_preceptor->setDivision($this->getObject());
          $division_preceptor->setPreceptorId($value);
          $division_preceptor->save();
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