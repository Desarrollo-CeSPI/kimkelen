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
 * DivisionStudent form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class DivisionStudentInscriptionForm extends sfFormPropel
{
  static $_students=array();

  public static function setAvailableStudentsForDivision(Division $division)
  {
    self::$_students = array_merge($division->getStudents(),SchoolBehaviourFactory::getInstance()->getAvailableStudentsForDivision($division, $filter_by_orientation = true));
  }

  public static function getCriteriaForAvailableStudentsForDivisionIds()
  {
    $ret = array();

    foreach (self::$_students as $st)
    {
      $ret[]=$st->getId();
    }

    $criteria = new Criteria();
    $criteria->add(StudentPeer::ID,$ret,Criteria::IN);
    $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::IS_ACTIVE, true);

    return $criteria;
  }

  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset', 'Tag', 'Url','Javascript'));

    $this->widgetSchema['id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['id'] = new sfValidatorPropelChoice(array('model' => 'Division'));

    DivisionStudentInscriptionForm::setAvailableStudentsForDivision($this->getObject());

    $this->widgetSchema['division_student_list'] = new csWidgetFormStudentMany(array('criteria'=> self::getCriteriaForAvailableStudentsForDivisionIds()));

    $this->getWidget('division_student_list')->setLabel('Alumnos');

    $this->validatorSchema['division_student_list'] = new sfValidatorPass();

 //   $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'validate'))));

    $this->validatorSchema->setOption('allow_extra_fields', true);

    $this->widgetSchema->setNameFormat('division[%s]');
  }

  public function getModelName()
  {
    return 'Division';
  }

  function validate ($validator, $values)
  {
    $student_ids = $values['division_student_list'];
    $result = true;
    $msg = 'No se pudieron hacer las inscripciones por que surgieron algunos errores.';
    $msg.= '<ul>';
    if (!is_null($student_ids))
    {
      foreach ($student_ids as $student_id)
      {
        $student = StudentPeer::retrieveByPk($student_id);
        if($student->isRegistered() && !is_null($student->isRegisteredInCareer($this->getObject()->getCareer())))
        {
          foreach ($this->getObject()->getCourses() as  $course)
          {
            if (!$student->hasApprovedCorrelativesForCourse($course))
            {
              $result = false;
              $msg.= '<li>El alumno ' . $student . ' no puede cursar: ' . $course . ' por que no tiene las correlativas necesarias. </li>';
            }
          }
        }
        else
        {
          $result = false;
          $msg.= '<li>El alumno ' . $student . ' no se encuentra inscripto en la carrera o en el año lectivo. </li>';
        }
      }

      $msg.= '</ul>';
      if (!$result)
        throw new sfValidatorError($validator, $msg);
    }

    return $values;

  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['division_student_list']))
    {
      $values = array();

      foreach ($this->object->getStudents() as $student)
      {
        $values[] = $student->getId();
      }

      $this->setDefault('division_student_list', $values);
    }
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->saveDivisionStudentList($con);
  }

  public function saveDivisionStudentList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['division_student_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }
    $con->beginTransaction();
    try
    {
      $values = $this->getValue('division_student_list');
      $this->getObject()->deleteStudents($con, $values);

      if (is_array($values))
      {
          foreach ($values as $value)
          {
            $division_student = new DivisionStudent();
            $division_student->setDivision($this->getObject());
            $division_student->setStudentId($value);
            $division_student->save($con);
          }
      }
      $con->commit();
     }
    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }
  }

}