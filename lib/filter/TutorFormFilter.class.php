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

    $this->setWidget('lastname', new sfWidgetFormInput());
    $this->setValidator('lastname', new sfValidatorString(array('required' => false)));

    $this->setWidget('firstname', new sfWidgetFormInput());
    $this->setValidator('firstname', new sfValidatorString(array('required' => false)));

    $this->setWidget('is_alive', new sfWidgetFormChoice(array('choices' => array('' => '', 1 => 'Sí', 2 => 'No'))));
    $this->setValidator('is_alive', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 2))));

    $this->setWidget('student', new dcWidgetFormPropelJQuerySearch(array('model' => 'Person', 'column' => array('lastname', 'firstname'), 'peer_method' => 'doSelectStudent')));
    $this->setValidator('student', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')));  

    $max = CareerPeer::getMaxYear();
    $years = array('' => '');
    for ($i = 1; $i <= $max; $i++)
      $years[$i] = $i;

    $this->setWidget('year', new sfWidgetFormChoice(array('choices' => $years)));
    $this->setValidator('year' , new sfValidatorChoice(array('choices' => array_keys($years), 'required' => false)));

    
    $this->setWidget('division_id', new sfWidgetFormPropelChoice(array('model' => 'Division', 'peer_method' => 'retrieveSchoolYearDivisions', 'add_empty' => true)));
    $this->setValidator('division_id', new sfValidatorPropelChoice(array('model' => 'Division', 'required' => false)));
    
    //widgets options
    $this->getWidgetSchema()->setHelp('lastname', 'Se filtrara por apellido del tutor.');
    $this->getWidgetSchema()->setHelp('firstname', 'Se filtrara por nombre del tutor.');
    $this->getWidgetSchema()->setHelp('identification_number', 'Se filtrara por numero de documento del tutor.');
    $this->getWidgetSchema()->setHelp('student', 'Se filtrara por el alumno a cargo.');
    $this->getWidgetSchema()->setHelp('division_id', 'Se filtrara por la division del alumno a cargo.');
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array('lastname' => 'text', 'firstname' => 'text', 'is_alive' => 'Boolean', 'student' => 'ForeignKey', 'division_id' => 'Number','year'=>'Number'));

  }

  public function addFirstnameColumnCriteria(Criteria $criteria, $field, $value)
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
    $criteria->add(StudentPeer::PERSON_ID, $value);
    $criteria->addJoin(StudentTutorPeer::STUDENT_ID, StudentPeer::ID);
    $criteria->addJoin(TutorPeer::ID, StudentTutorPeer::TUTOR_ID);

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
  
  public function addYearColumnCriteria(Criteria $criteria , $field, $values)
  {
    if ($values)
    {
      $criteria->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
      $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
      $criteria->add(StudentCareerSchoolYearPeer::YEAR, $values);
      $criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
      $criteria->addJoin(StudentPeer::ID, StudentTutorPeer::STUDENT_ID);
      $criteria->addJoin(StudentTutorPeer::TUTOR_ID, TutorPeer::ID);
      
    }
  }

}
