<?php

/**
 * ExaminationSubject filter form.
 *
 * @package    sistema de alumnos
 * @subpackage filter
 * @author     Your name here
 */
class ExaminationSubjectFormFilter extends BaseExaminationSubjectFormFilter
{
  public function configure()
  {
    unset($this['examination_id'], $this['career_subject_school_year_id'],  $this['examination_subject_teacher_list']);

    $this->setWidget('year', new sfWidgetFormFilterInput(array('with_empty' => false)));
    $this->setValidator('year', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));

  }

  public function getFields()
  {
    return array('year' => 'Number', 'is_closed' => 'Boolean');
  }

  public function addYearColumnCriteria($criteria, $field, $value)
  {
    if (! is_null($value['text']))
    {
      $criteria->addJoin(ExaminationSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
      $criteria->add(CareerSubjectPeer::YEAR, $value['text']);
    }
  }
}
