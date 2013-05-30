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
 * FinalExaminationSubjectStudentsForm to inscribe students to a FinalExaminationSubject
 *
 * @author gramirez
 */
class FinalExaminationSubjectStudentsForm extends  BaseFormPropel
{

  static $_final_students_subject = array();

  public static function setAvailableStudents(FinalExaminationSubject $final_examination_subject)
  {
    self::$_final_students_subject = array_merge($final_examination_subject->getStudents(),
    SchoolBehaviourFactory::getInstance()->getAvailableStudentsForFinalExaminationSubject($final_examination_subject));
  }

  public static function getCriteriaForAvailableStudents()
  {
    $ret=array();
    foreach (self::$_final_students_subject as $sr)
    {
      $ret[]=$sr->getId();
    }

    $criteria = new Criteria();
    $criteria->add(StudentPeer::ID,$ret,Criteria::IN);
    $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::IS_ACTIVE, true);

    return $criteria;
  }

    //put your code here
  public function configure()
  {
    //formatter
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    FinalExaminationSubjectStudentsForm::setAvailableStudents($this->getObject());

    $this->setWidget('final_examination_subject_id', new sfWidgetFormInputHidden());
    $this->setValidator('final_examination_subject_id', new sfValidatorPropelChoice(array('model' => 'FinalExaminationSubject', 'required' => true)));

    $this->setWidget('student_list',new csWidgetFormStudentMany(array('criteria'=> $this->getCriteriaForAvailableStudents())));
    $this->setValidator('student_list', new sfValidatorPropelChoice(array('model' => 'Student', 'required' => true, 'multiple' => true)));

    $this->widgetSchema->setNameFormat('final_examination_subject[%s]');
  }

  public function getModelName()
  {
    return 'FinalExaminationSubject';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['student_list']))
    {
      $values = array();
      foreach ($this->object->getStudents() as $student)
      {
        $values[] = $student->getId();
      }

      $this->setDefault('student_list', $values);
    }
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);
    $this->saveStudentList($con);
  }

  public function saveStudentList($con = null)
  {
    
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['student_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    foreach ($this->getObject()->getFinalExaminationSubjectStudents() as $fess)
    {
      $fess->delete($con);
    }

    $values = $this->getValue('student_list');    
    
    if (is_array($values))
    {
      $con->beginTransaction();

      try
      {
        foreach ($values as $value)
        {
          $student = StudentPeer::retrieveByPK($value);
          $final_examination_subject_student = new FinalExaminationSubjectStudent();
          $final_examination_subject_student->setFinalExaminationSubject($this->getObject());
          $final_examination_subject_student->setCareerSubject($student->getCareerSubjectForFinalExaminationSubject($this->getObject()));
          $final_examination_subject_student->setStudent($student);          
          $final_examination_subject_student->save($con);
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

}