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

class CoursePreceptorsForm extends BaseFormPropel
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $this->setWidget("preceptors", new sfWidgetFormPropelChoice(array(
      "model" => "Personal",
      'peer_method' => 'doSelectActivePreceptor',
      "multiple"  => true,
      "renderer_class"  => "csWidgetFormSelectDoubleList",
    )));

    $this->getWidgetSchema()->setLabel("preceptors", "Preceptores");

    $this->setValidator("preceptors" , new sfValidatorPropelChoice(array(
      "model" => "Personal",
      "multiple" => true,
      'required' => false
    )));

    $this->getWidgetSchema()->setNameFormat('course_preceptors[%s]');
  }

  public function getModelName()
  {
    return 'Course';
  }

  public function updateDefaultsFromObject()
  {
    $course= $this->getObject();

    $values = array();
    foreach ($course->getCoursePreceptors() as $course_preceptor)
    {
      $values[] = $course_preceptor->getPreceptorId();
    }
    $this->setDefault("preceptors", $values);
  }

  protected function doSave($con = null)
  {
    $course = $this->getObject();

    $con = (is_null($con)) ? $this->getConnection() : $con;
    try
    {
      $con->beginTransaction();

      foreach ($course->getCoursePreceptors () as $course_preceptor)
      {        
        $course_preceptor->delete($con);
      }

      if (isset($this->values["preceptors"]))
      {
        foreach ($this->values["preceptors"] as $preceptor_id)
        {          
          $course_preceptor = new CoursePreceptor();
          $course_preceptor->setPreceptorId($preceptor_id);
          $course_preceptor->setCourse($course);
          $course_preceptor->save($con);
        }
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      throw $e;
      $con->rollBack();
    }
  }
}