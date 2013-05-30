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
 * Classroom form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class ClassroomForm extends BaseClassroomForm
{

  public function configure()
  {
    $this->setWidget('resources', new sfWidgetFormSelectDoubleList(array('choices' => ResourcesPeer::getChoices(),
                                                                                              'label_unassociated' => 'No seleccionados',
                                                                                              'label_associated' => 'Seleccionados',
                                                                                              'associate'=>'<img src="../../../sfFormExtraPlugin/images/next.png" alt="Seleccionar" />',
                                                                                              'unassociate'=> '<img src="../../../sfFormExtraPlugin/images/previous.png" alt="Desseleccionar" />')));




    $this->setValidator('resources', new sfValidatorPropelChoiceMany(array('model' => 'Resources', 'required' => false)));
    $this->validatorSchema->getPostValidator()->setMessage('invalid', 'Ya existe un aula con éste nombre.');

  }

 public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['resources']))
    {
      $values = array();
      foreach ($this->object->getResources() as $obj)
      {
        $values[] = $obj->getId();
      }

      $this->setDefault('resources', $values);
    }

  }
  protected function doSave($con = null)
  {
    parent::doSave($con);

    ClassroomResourcesPeer::doDeleteByClassroom($this->getObject()->getId());

    foreach ($this->getValue('resources') as $resource_id)
    {
      $cs = new ClassroomResources();
      $cs->setResourceId($resource_id);
      $cs->setClassroom($this->getObject());
      $cs->save($con);
    }


  }

}