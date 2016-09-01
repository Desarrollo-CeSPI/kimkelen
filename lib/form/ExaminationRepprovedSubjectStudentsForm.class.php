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
 * Description of ExaminationRepprovedSubjectStudentsForm to inscribe students to a ExaminationRepprovedSubject
 *
 * @author gramirez
 */
class ExaminationRepprovedSubjectStudentsForm extends  BaseFormPropel
{

  static $_students_repproved_course_subject = array();

  public static function setAvailableStudents(ExaminationRepprovedSubject $examination_repproved_subject)
  {
    self::$_students_repproved_course_subject = array_merge($examination_repproved_subject->getStudents(),
    SchoolBehaviourFactory::getInstance()->getAvailableStudentsForExaminationRepprovedSubject($examination_repproved_subject));
  }

  public static function getCriteriaForAvailableStudents()
  {
    $ret=array();
    foreach (self::$_students_repproved_course_subject as $sr)
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

    ExaminationRepprovedSubjectStudentsForm::setAvailableStudents($this->getObject());

    $this->setWidget('examination_repproved_subject_id', new sfWidgetFormInputHidden());
    $this->setValidator('examination_repproved_subject_id', new sfValidatorPropelChoice(array('model' => 'ExaminationRepprovedSubject', 'required' => true)));

    $this->setWidget('student_list',new csWidgetFormStudentMany(array('criteria'=> $this->getCriteriaForAvailableStudents())));
    $this->setValidator('student_list', new sfValidatorPropelChoice(array('model' => 'Student', 'required' => true, 'multiple' => true)));

    $this->widgetSchema->setNameFormat('examination_repproved_subject[%s]');
  }

  public function getModelName()
  {
    return 'ExaminationRepprovedSubject';
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

    foreach ($this->getObject()->getStudentExaminationRepprovedSubjects() as $sr)
    {
      $sr->delete($con);
    }

    $values = $this->getValue('student_list');
    if (is_array($values))
    {
      $con->beginTransaction();

      try
      {
        foreach ($values as $value)
        {
          $student_repproved = StudentRepprovedCourseSubjectPeer::retrieveByCareerSubjectIdAndStudentId($this->getObject()->getCareerSubjectId(), $value);
          $student_examination_repproved_subject = new StudentExaminationRepprovedSubject();
          $student_examination_repproved_subject->setStudentRepprovedCourseSubject($student_repproved);
          $student_examination_repproved_subject->setExaminationRepprovedSubject($this->getObject());
          $student_examination_repproved_subject->save($con);
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
?>
