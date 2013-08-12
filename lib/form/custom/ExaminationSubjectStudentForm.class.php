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
class ExaminationSubjectStudentForm extends sfFormPropel
{
  //static $_students = array();

  public function getAvailableStudentsForDivision()
  {
    return array_merge($this->getObject()->getStudents(), SchoolBehaviourFactory::getInstance()->getAvailableStudentsForExaminationSubject($this->getObject()));
  }

  public function getCriteriaForAvailableStudentsForExaminationSubjectIds()
  {
    $ret = array();

    foreach ($this->getAvailableStudentsForDivision() as $st)
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
    $this->validatorSchema['id'] = new sfValidatorPropelChoice(array('model' => 'ExaminationSubject'));


    //self::setAvailableStudentsForDivision($this->getObject());

    $this->widgetSchema['examination_subject_student_list'] = new csWidgetFormStudentMany(array('criteria'=> $this->getCriteriaForAvailableStudentsForExaminationSubjectIds()));

    $this->getWidget('examination_subject_student_list')->setLabel('Alumnos');

    $this->validatorSchema['examination_subject_student_list'] = new sfValidatorPass();

 //   $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'validate'))));

    $this->validatorSchema->setOption('allow_extra_fields', true);

    $this->widgetSchema->setNameFormat('examination_subject[%s]');

    $this->getWidgetSchema()->setHelp('examination_subject_student_list', 'If you delete from the examination a student who already has marks, these will also be eliminated.');
  }

  public function getModelName()
  {
    return 'ExaminationSubject';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['examination_subject_student_list']))
    {
      $values = array();
      foreach ($this->object->getStudents() as $student)
      {
        $values[] = $student->getId();
      }

      $this->setDefault('examination_subject_student_list', $values);
    }
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->saveExaminationSubjectStudentList($con);
  }

  public function saveExaminationSubjectStudentList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['examination_subject_student_list']))
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
      $values = $this->getValue('examination_subject_student_list');

      foreach ($this->getObject()->getCourseSubjectStudentExaminations() as $csse)
      {
        if(!is_array($values))
        {
          $values = array();
        }
        if(!in_array($csse->getStudent()->getId(), $values))
        {
          $csse->delete($con);
        }
        else
        {
          unset($values[array_search($csse->getStudent()->getId(), $values)]);
        }
      }

      if (is_array($values))
      {
          foreach ($values as $student_id)
          {
            $course_subject_student_examination = new CourseSubjectStudentExamination();
            $course_subject_student_examination->setExaminationSubject($this->getObject());
            $course_subject_student_examination->setExaminationNumber($this->getObject()->getExamination()->getExaminationNumber());
            $course_subject_student = CourseSubjectStudentPeer::retrieveByCareerSubjectSchoolYearAndStudent($this->getObject()->getCareerSubjectSchoolYear(), $student_id);

            $course_subject_student_examination->setCourseSubjectStudent($course_subject_student);

            $course_subject_student_examination->save($con);
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