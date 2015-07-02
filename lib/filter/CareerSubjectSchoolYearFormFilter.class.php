<?php

/**
 * CareerSubjectSchoolYear filter form.
 *
 * @package    sistema de alumnos
 * @subpackage filter
 * @author     Your name here
 */
class CareerSubjectSchoolYearFormFilter extends BaseCareerSubjectSchoolYearFormFilter
{
  public function configure()
  {
    unset(
      $this['subject_configuration_id'],
      $this['career_school_year_id']
    );

    //widgets
    $this->widgetSchema["year"] = new dcWidgetFormFilterInputRange();
    $this->setWidget('career_subject_id', new sfWidgetFormInput());

    $this->validatorSchema["career_subject_id"] = new sfValidatorString(array("required" => false));
    $this->validatorSchema["year"] = new sfValidatorPass();

    $this->getWidget('index_sort')->setOption('with_empty', false);

    $this->getWidgetSchema()->moveField("year", "before", "career_subject_school_year_tag_list");
  }

  public function getFields()
  {
    return array_merge(array('year' => 'Number'), parent::getFields());
  }

  public function addCareerSubjectIdColumnCriteria($criteria, $field, $value)
  {
    if (!empty($value))
    {
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
      $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
      $criteria->add(SubjectPeer::NAME, "%$value%", Criteria::LIKE);
    }
  }

  public function addYearColumnCriteria($criteria, $field, $values)
  {
    if ($values["from"] != "")
    {
      $criteria->addAnd(CareerSubjectPeer::YEAR, $values["from"], Criteria::GREATER_EQUAL);
      $criteria->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    }

    if ($values["to"] != "")
    {
      $criteria->addAnd(CareerSubjectPeer::YEAR, $values["to"], Criteria::LESS_EQUAL);
      $criteria->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    }
  }

}
