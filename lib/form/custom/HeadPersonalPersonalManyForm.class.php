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
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HeadPersonalPersonalManyForm
 *
 * @author gramirez
 */
class HeadPersonalPersonalManyForm extends sfFormPropel
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $this->setWidget('id', new sfWidgetFormInputHidden());
    $this->setValidator('id', new sfValidatorPropelChoice(array('model' => 'Personal')));

    $this->setWidget('preceptor_list', new sfWidgetFormPropelChoice(array(
      'model'     => 'Personal',
      'peer_method' => 'doSelectActivePreceptor',
      'multiple'  => true,
      "renderer_class"  => "csWidgetFormSelectDoubleList",
      )));
    
    $this->setValidator('preceptor_list', new sfValidatorPropelChoice(array(
      'model'    => 'Personal',
      'multiple' => true,
      'required' => false,
    )));
    $this->getWidget('preceptor_list')->setLabel('Preceptores');
    
    $this->widgetSchema->setNameFormat('head_personal_personal[%s]');
  }

  public function getModelName()
  {
    return 'Personal';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['preceptor_list']))
    {
      $values = array();
      foreach ($this->object->getHeadPersonalPersonals() as $head_personal_personal)
      {
        $values[] = $head_personal_personal->getPersonalId();
      }

      $this->setDefault('preceptor_list', $values);
    }
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->savePreceptorList($con);
  }

  public function savePreceptorList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['preceptor_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    foreach ($this->getObject()->getHeadPersonalPersonals() as $head_personal_personal)
    {
      $head_personal_personal->delete($con);
    }

    $values = $this->getValue('preceptor_list');
    if (is_array($values))
    {
      $con->beginTransaction();
      try
      {

        foreach ($values as $value)
        {
          $head_personal_personal = new HeadPersonalPersonal();
          $head_personal_personal->setHeadPersonalId($this->getObject()->getId());
          $head_personal_personal->setPersonalId($value);
          $head_personal_personal->save($con);
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