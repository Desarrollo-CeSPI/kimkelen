<?php

/**
 * Tutor filter form.
 *
 * @package    conservatorio
 * @subpackage filter
 * @author     Desarrollo CeSPI
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class TutorFormFilter extends BaseTutorFormFilter
{

  public function configure()
  {
    unset($this['person_id'], $this['person'], $this['occupation_category_id'], $this['nationality'], $this['occupation_id'], $this['study_id']);

    $this->setWidget('student', new sfWidgetFormInput());
    $this->setValidator('student', new sfValidatorString(array('required' => false)));

    $this->setWidget('lastname', new sfWidgetFormInput());
    $this->setValidator('lastname', new sfValidatorString(array('required' => false)));
    $this->setWidget('name', new sfWidgetFormInput());
    $this->setValidator('name', new sfValidatorString(array('required' => false)));

    $this->setWidget('division_id', new sfWidgetFormPropelChoice(array('model' => 'Division', 'peer_method' => 'retrieveSchoolYearDivisions', 'add_empty' => true)));
    $this->setValidator('division_id', new sfValidatorPropelChoice(array('model' => 'Division', 'required' => false)));

  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array('student' => 'Text', 'division_id' => 'Number', 'lastname' => 'text','name' => 'text'));

  }

  public function addNameColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = trim($value);
    if ($value != '')
    {
      $criteria->addJoin(TutorPeer::PERSON_ID, PersonPeer::ID);
      $criteria->add(PersonPeer::FIRSTNAME, '%'.$value.'%',Criteria::LIKE);
    }

  }
  public function addLastnameColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = trim($value);
    if ($value != '')
    {
      $criteria->addJoin(TutorPeer::PERSON_ID, PersonPeer::ID);
      $criteria->add(PersonPeer::LASTNAME, '%'.$value.'%',Criteria::LIKE);
    }

  }

  public function addStudentColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = trim($value);
    if ($value != '')
    {
      $student = explode(', ', $value);
      $c = new Criteria();
      $c->setIgnoreCase(true);
      $c->addJoin(StudentTutorPeer::TUTOR_ID, TutorPeer::ID);
      $c->addJoin(StudentTutorPeer::STUDENT_ID, StudentPeer::ID);
      $c->addJoin(PersonPeer::ID, StudentPeer::PERSON_ID);

      if (key_exists(1, $student))
      {
        $criterion = $c->getNewCriterion(PersonPeer::FIRSTNAME, $student[1], Criteria::EQUAL);
        $criterion->addAnd($c->getNewCriterion(PersonPeer::LASTNAME, $student[0], Criteria::EQUAL));
      }
      else
      {
        $student[0] = "%$student[0]%";
        $criterion = $c->getNewCriterion(PersonPeer::FIRSTNAME, $student[0], Criteria::LIKE);
        $criterion->addOr($c->getNewCriterion(PersonPeer::LASTNAME, $student[0], Criteria::LIKE));
      }

      $criterion->addOr($c->getNewCriterion(PersonPeer::IDENTIFICATION_NUMBER, $value, Criteria::LIKE));
      $c->add($criterion);
      $c->clearSelectColumns();
      $c->addSelectColumn(TutorPeer::ID);
      $stmt = StudentPeer::doSelectStmt($c);
      $tutor_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
      $criteria->add(TutorPeer::ID, $tutor_ids, Criteria::IN);
    }

  }

  public function addDivisionIdColumnCriteria(Criteria $criteria, $field, $value)
  {
    if ($value)
    {
      $criteria->add(DivisionPeer::ID, $value);
      $criteria->addJoin(DivisionPeer::ID, DivisionStudentPeer::DIVISION_ID);
      $criteria->addJoin(StudentPeer::ID, DivisionStudentPeer::STUDENT_ID);
      $criteria->addJoin(StudentPeer::ID, StudentTutorPeer::STUDENT_ID);
      $criteria->addJoin(StudentTutorPeer::TUTOR_ID, TutorPeer::ID);
    }

  }

}
