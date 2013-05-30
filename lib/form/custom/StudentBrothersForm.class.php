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
 * StudentBrothers form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Cespi desarollo
 */
class StudentBrothersForm extends sfFormPropel
{
  static $_brothers=array();

  public static function setBrothersForStudent(Student $student)
  {
    $c = new Criteria();
    $c->add(StudentPeer::ID, $student->getId(), Criteria::NOT_EQUAL);
    self::$_brothers = array_merge($student->getBrothers(), StudentPeer::doSelect($c));
  }

  public static function getStudentsForBrotherIds()
  {
    $ret=array();
    foreach (self::$_brothers as $st)
    {
      $ret[]=$st->getId();
    }

    $criteria = new Criteria();
    $criteria->add(StudentPeer::ID,$ret,Criteria::IN);
    return $criteria;
  }

  public function configure()
  {
    //Set the formatter to the form
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $this->setWidget('id', new sfWidgetFormInputHidden());
    $this->setValidator('id', new sfValidatorPropelChoice(array('model' => 'Student')));
    $this->setDefault('id', $this->getObject()->getId());

    StudentBrothersForm::setBrothersForStudent($this->getObject());

    $available_criteria = self::getStudentsForBrotherIds();
    $student_filters_criteria = new csWidgetFormStudentManyFilterCriteriaAllStudents();
    $student_filters_criteria->setOption('group_letters_count', 1);
    $this->setWidget('brother_list', new csWidgetFormStudentMany(array('criteria'=> $available_criteria, 'filter_criterias' => array($student_filters_criteria))));
    $this->setValidator('brother_list', new sfValidatorPass());

    $this->widgetSchema->setNameFormat('brotherhood[%s]');
  }

  public function getModelName()
  {
    return 'Student';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();
    if (isset($this->widgetSchema['brother_list']))
    {
      $values = array();
      foreach ($this->object->getBrotherhoodsRelatedByStudentId() as $brother)
      {
        $values[] = $brother->getBrotherId();
      }
      $this->setDefault('brother_list', $values);
    }
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);
    $this->saveStudentBrotherList($con);
  }

  public function saveStudentBrotherList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['brother_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    foreach ($this->getObject()->getBrotherhoodsRelatedByStudentId() as $brotherhood)
    {
      $brotherhood->delete($con);
    }

    foreach ($this->getObject()->getBrotherhoodsRelatedByBrotherId() as $brotherhood)
    {
      $brotherhood->delete($con);
    }

    $values = $this->getValue('brother_list');
    
    if (is_array($values))
    {
      $con->beginTransaction();
      try
      {
        foreach ($values as $value)
        {
          $brotherhood = new Brotherhood();
          $brotherhood->setStudentId($this->getObject()->getId());
          $brotherhood->setBrotherId($value);
          $brotherhood->save($con);
          
          $brotherhood_1 = new Brotherhood();
          $brotherhood_1->setStudentId($value);
          $brotherhood_1->setBrotherId($this->getObject()->getId());
          $brotherhood_1->save($con);
        }
        $con->commit();
      }
      catch (Exception $e)
      {
        $con->rollBack();
      }
    }
  }

/*
  public function  getJavaScripts()
  {
    $javascript = '';
    $javascript .= parent::getJavaScripts();
    //$javascript .= '; jQuery(document).ready(function '
    return $javascript;
  }
*/

}