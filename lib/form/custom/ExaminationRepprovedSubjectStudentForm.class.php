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
 * Description of ExaminationSubjectStudentForm
 *
 * @author gramirez
 */
class ExaminationRepprovedSubjectStudentForm extends sfFormPropel
{
  static $_students = array();

  public static function setAvailableStudentsForDivision($examination_repproved_subject)
  {
    self::$_students = StudentRepprovedCourseSubjectPeer::getAvailableStudentsForExaminationRepprovedSubject($examination_repproved_subject);
  }

  public static function getCriteriaForAvailableStudentsForExaminationSubjectIds()
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
    $this->validatorSchema['id'] = new sfValidatorPropelChoice(array('model' => 'ExaminationRepprovedSubject'));


    self::setAvailableStudentsForDivision($this->getObject());

    $name = 'examination_repproved_subject_student_list';
    $this->setWidget($name, new csWidgetFormStudentMany(array('criteria'=> self::getCriteriaForAvailableStudentsForExaminationSubjectIds())));

    $this->getWidget($name)->setLabel('Alumnos');

    $this->validatorSchema[$name] = new sfValidatorPass();

    $this->validatorSchema->setOption('allow_extra_fields', true);

    $this->widgetSchema->setNameFormat('examination_repproved_subject[%s]');
    
    $this->getWidgetSchema()->setHelp('examination_repproved_subject_student_list', 'If you delete from the examination a student who already has marks, these will also be eliminated.');
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['examination_repproved_subject_student_list']))
    {
      $values = array();
      foreach ($this->object->getStudents() as $student)
      {
        $values[] = $student->getId();
      }

      $this->setDefault('examination_repproved_subject_student_list', $values);
    }
  }

  public function getModelName()
  {
    return 'ExaminationRepprovedSubject';
  }

   protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->saveExaminationRepprovedSubjectStudentList($con);
  }

  public function saveExaminationRepprovedSubjectStudentList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['examination_repproved_subject_student_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = Propel::getConnection();
    }

    $con->beginTransaction();

    try
    {
      $values = $this->getValue('examination_repproved_subject_student_list');
      
      foreach ($this->getObject()->getStudentExaminationRepprovedSubjects() as $sers)
      {
        if(!is_array($values))
        {
          $values = array();
        }
        if(!in_array($sers->getStudent()->getId(), $values))
        {
          $sers->delete($con);
        }
        else
        {
          unset($values[array_search($sers->getStudent()->getId(), $values)]);
        }
      }
      $career_subject_id = $this->getObject()->getCareerSubjectId();

      if (is_array($values))
      {
          foreach ($values as $student_id)
          {
            $student_repproved_course_subject = StudentRepprovedCourseSubjectPeer::retrieveByCareerSubjectIdAndStudentId($career_subject_id , $student_id);

            $student_examination_repproved_subject = new StudentExaminationRepprovedSubject();
            $student_examination_repproved_subject->setExaminationRepprovedSubject($this->getObject());
            $student_examination_repproved_subject->setStudentRepprovedCourseSubject($student_repproved_course_subject);

            $student_repproved_course_subject->save($con);
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
