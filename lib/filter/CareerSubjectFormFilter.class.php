<?php

/**
 * CareerSubject filter form.
 *
 * @package    conservatorio
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class CareerSubjectFormFilter extends BaseCareerSubjectFormFilter
{
  public function configure()
  {
    unset(
      $this["created_at"],
      $this["career_id"],
      $this["has_correlative_previous_year"],
      $this["student_career_subject_allowed_list"],
      $this["sub_orientation_id"],
      $this["student_career_subject_allowed_pathway_list"]
    );

    $this->widgetSchema["subject_id"] = new sfWidgetFormInput();

    $this->widgetSchema["year"] = new dcWidgetFormFilterInputRange();

    $this->validatorSchema["subject_id"] = new sfValidatorString(array(
      "required" => false
    ));

    $this->setWidget('has_options', new sfWidgetFormChoice(array('choices' => array('' => 'si o no', 1 => 'si', 0 => 'no'))));
    $this->setValidator('has_options', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $this->setWidget('is_option', new sfWidgetFormChoice(array('choices' => array('' => 'si o no', 1 => 'si', 0 => 'no'))));
    $this->setValidator('is_option', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $this->validatorSchema["year"] = new sfValidatorPass();

    $widget = new sfWidgetFormPropelChoice(array(
      'model' => 'SubOrientation',
      'add_empty' => true
    ));

  }

  public function addSubjectIdColumnCriteria($criteria, $field, $value)
  {
    if (!empty($value))
    {
      $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
      $criteria->add(SubjectPeer::NAME, "%$value%", Criteria::LIKE);
    }
  }

  public function addYearColumnCriteria($criteria, $field, $values)
  {
    if ($values["from"] != "")
    {
      $criteria->addAnd(CareerSubjectPeer::YEAR, $values["from"], Criteria::GREATER_EQUAL);
    }

    if ($values["to"] != "")
    {
      $criteria->addAnd(CareerSubjectPeer::YEAR, $values["to"], Criteria::LESS_EQUAL);
    }
  }
}
