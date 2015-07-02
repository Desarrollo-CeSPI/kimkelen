<?php

/**
 * Career filter form.
 *
 * @package    conservatorio
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class CareerFormFilter extends BaseCareerFormFilter
{
  public function configure()
  {
    $career_status = new CareerStatus();

    $this->widgetSchema['career_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->widgetSchema['plan_name']  = new sfWidgetFormFilterInput(array('with_empty' => false));
    
    $this->widgetSchema['career_status_id'] = new sfWidgetFormChoice(array(
      'choices' => $career_status->getOptions('Indistinto')
    ));

    $this->setValidator('career_status_id', new sfValidatorChoice(array(
      'choices'  => $career_status->getKeys(),
      'required' => false
    )));
  }

  public function addCareerStatusIdColumnCriteria($criteria, $field, $value)
  {
    $criteria->add(CareerPeer::CAREER_STATUS_ID, $value);
  }

}
