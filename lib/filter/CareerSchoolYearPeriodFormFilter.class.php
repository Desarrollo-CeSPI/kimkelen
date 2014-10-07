<?php

/**
 * CareerSchoolYearPeriod filter form.
 *
 * @package    sistema de alumnos
 * @subpackage filter
 * @author     Your name here
 */
class CareerSchoolYearPeriodFormFilter extends BaseCareerSchoolYearPeriodFormFilter
{
  public function configure()
  {
    $this->unsetFields();
    $this->setWidget('is_closed', new sfWidgetFormChoice(array('choices' => array('' => 'si o no', 1 => 'sí', 0 => 'no'))));
    $this->setValidator('is_closed', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $course_type = StudentCareerSchoolYearStatus::getInstance('CourseType');
    $this->setWidget('course_type', new sfWidgetFormChoice(array('choices' => $course_type->getOptions())));
    $this->setValidator('course_type', new sfValidatorChoice(array('choices' => $course_type->getKeys(), 'required' => false)));
  }

  public function unsetFields(){
    unset(
      $this['career_school_year_period_id'], $this['short_name'], $this['start_at'], $this['end_at'], $this['max_absences']
    );
  }

  public function addCourseTypeColumnCriteria(Criteria $criteria, $field, $values)
  {

    $criteria->add(CareerSchoolYearPeriodPeer::COURSE_TYPE, $values);
  }
}