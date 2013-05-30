<?php

/**
 * Personal filter form.
 *
 * @package    conservatorio
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class PersonalFormFilter extends BasePersonalFormFilter
{
  public function removeFields()
  {
    unset(
      $this['person_id'],
      $this['personal_type'],
      $this['file_number'],
      $this['aging_institution'],
      $this['salary']
    );
  }

  public function configure()
  {
    $this->removeFields();
    $this->setWidget('person', new sfWidgetFormInput());
    $this->setValidator('person', new sfValidatorString(array('required' => false)));
    $this->getWidgetSchema()->setLabel('person', 'Que contenga el nombre');
    $this->getWidgetSchema()->setHelp('person', 'Se filtrarán todas las personas que contengan lo ingresado en el nombre o apellido o numero de documento');

    $this->setWidget('username', new sfWidgetFormInput());
    $this->setValidator('username', new sfValidatorString(array('required' => false)));
    $this->getWidgetSchema()->setHelp('username', 'Se filtrarán por el nombre de usuario');
  }

  public function addUsernameColumnCriteria(Criteria $criteria, $field, $value)
  {
     $value = trim($value);
    if ($value != '')
    {
      $criteria->setIgnoreCase(true);
      $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
      $criteria->addJoin(PersonPeer::USER_ID,  sfGuardUserPeer::ID );
      $criterion = $criteria->getNewCriterion(sfGuardUserPeer::USERNAME, $value, Criteria::LIKE);
      $criteria->add($criterion);
    }
  }

  public function addPersonColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = trim($value);
    if ($value != '')
    {
      $value = "%$value%";
      $criteria->setIgnoreCase(true);
      $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
      $criterion = $criteria->getNewCriterion(PersonPeer::FIRSTNAME, $value, Criteria::LIKE);
      $criterion->addOr($criteria->getNewCriterion(PersonPeer::LASTNAME, $value, Criteria::LIKE));
      $criterion->addOr($criteria->getNewCriterion(PersonPeer::IDENTIFICATION_NUMBER, $value, Criteria::LIKE));
      $criteria->add($criterion);
    }

  }
  public function getFields()
  {
    return array_merge(parent::getFields(), array('person' => 'Text','username' => 'Text'));
  }
}
