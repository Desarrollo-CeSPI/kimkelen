<?php

/**
 * Holiday filter form.
 *
 * @package    symfony
 * @subpackage filter
 * @author     Your name here
 */
class HolidayFormFilter extends BaseHolidayFormFilter
{
  public function configure()
  {
    unset($this['description']);
    $this->setWidget('day', new csWidgetFormDateInput());
    $this->setValidator('day', new mtValidatorDateString(array('required' => false)));

    $this->setWidget('school_year', new sfWidgetFormPropelChoice(array('model' => 'SchoolYear', 'peer_method' => 'doSelect', 'add_empty' => true)));
    $this->setValidator('school_year', new sfValidatorPropelChoice(array('required' => false, 'model' => 'SchoolYear', 'column' => 'id')));
    $this->getWidgetSchema()->setLabel('school_year', 'Holidays of school year');
  }

    /**
   * This method filters all holidays for the school year given.
   *
   * @param Criteria $criteria
   * @param string $field
   * @param array $values
   */
  public function addSchoolYearColumnCriteria(Criteria $criteria, $field, $values)
  {
    $school_year = SchoolYearPeer::retrieveByPk($values);
    $date = date('Y-m-d', strtotime("first day of January " . $school_year->getYear()));
    $end_date = date('Y-m-d', strtotime("last day of December " . $school_year->getYear()));

    $criteria->add(HolidayPeer::DAY, $date, Criteria::GREATER_EQUAL);
    $criteria->addAnd($criteria->getNewCriterion(HolidayPeer::DAY, $end_date, Criteria::LESS_EQUAL));
  }

    public function getFields()
  {
    return array_merge(parent::getFields(), array(
        'day' => 'Date',
        'school_year' => 'Number'));
  }
}
