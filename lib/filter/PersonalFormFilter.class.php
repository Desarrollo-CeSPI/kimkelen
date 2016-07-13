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

    //widgets
    $this->setWidget('lastname', new sfWidgetFormInput());
    $this->setValidator('lastname', new sfValidatorString(array('required' => false)));

    $this->setWidget('firstname', new sfWidgetFormInput());
    $this->setValidator('firstname', new sfValidatorString(array('required' => false)));

    $this->setWidget('identification_number', new sfWidgetFormInput());
    $this->setValidator('identification_number', new sfValidatorNumber(array('required' => false)));

    $this->setWidget('username', new sfWidgetFormInput());
    $this->setValidator('username', new sfValidatorString(array('required' => false)));

    $this->setWidget('is_active', new sfWidgetFormChoice(array('choices' => array('' => '', 1 => 'Sí', 0 => 'No'))));
    $this->setValidator('is_active', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $this->setWidget('username', new sfWidgetFormInput());
    $this->setValidator('username', new sfValidatorString(array('required' => false)));

        //widgets options
    $this->getWidgetSchema()->setLabel('identification_number', 'Documento');
    $this->getWidgetSchema()->setHelp('lastname', 'Se filtrara por apellido del preceptor.');
    $this->getWidgetSchema()->setHelp('firstname', 'Se filtrara por nombre del preceptor.');
    $this->getWidgetSchema()->setHelp('identification_number', 'Se filtrara por numero de documento del preceptor.');
    $this->getWidgetSchema()->setHelp('username', 'Se filtrara por el nombre de usuario del preceptor');

  }

  public function addLastnameColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = trim($value);
    if ($value != '')
    {
      $value = "%$value%";
      $criteria->setIgnoreCase(true);
      $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
      $criterion = $criteria->getNewCriterion(PersonPeer::LASTNAME, $value, Criteria::LIKE);
      $criteria->add($criterion);
    }

  }

  public function addFirstnameColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = trim($value);
    if ($value != '')
    {
      $value = "%$value%";
      $criteria->setIgnoreCase(true);
      $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
      $criterion = $criteria->getNewCriterion(PersonPeer::FIRSTNAME, $value, Criteria::LIKE);
      $criteria->add($criterion);
    }

  }

  public function addIdentificationNumberColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = trim($value);
    if ($value != '')
    {
      $value = "%$value%";
      $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
      $criterion = $criteria->getNewCriterion(PersonPeer::IDENTIFICATION_NUMBER, $value, Criteria::LIKE);
      $criteria->add($criterion);
      $criteria->setDistinct();
    }

  }

  public function addUsernameColumnCriteria(Criteria $criteria, $field, $value)
  {
     $value = trim($value);
    if ($value != '')
    {
      $value = "%$value%";
      $criteria->setIgnoreCase(true);
      $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
      $criteria->addJoin(PersonPeer::USER_ID,  sfGuardUserPeer::ID );
      $criterion = $criteria->getNewCriterion(sfGuardUserPeer::USERNAME, $value, Criteria::LIKE);
      $criteria->add($criterion);
    }
  }

  public function addIsActiveColumnCriteria(Criteria $criteria, $field, $value)
  {
    if ($value != '')
    {
      $c = new Criteria();
      $c->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
      $c->add(PersonPeer::IS_ACTIVE, 1);
      $c->clearSelectColumns();
      $c->addSelectColumn(PersonPeer::ID);
      $stmt = PersonPeer::doSelectStmt($c);
      $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

      if($value == '1')
      {
        $criteria->add(PersonalPeer::PERSON_ID, $ids, Criteria::IN);
      }
      else
      {
        $criteria->add(PersonalPeer::PERSON_ID, $ids, Criteria::NOT_IN);
      }
    }

  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array('lastname' => 'Text', 'firstname' => 'Text', 'identification_number' => 'Number', 'username' => 'Text', 'is_active' => 'Boolean'));
  }
}