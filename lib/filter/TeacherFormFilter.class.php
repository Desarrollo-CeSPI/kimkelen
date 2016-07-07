<?php

/**
 * Teacher filter form.
 *
 * @package    conservatorio
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class TeacherFormFilter extends BaseTeacherFormFilter
{

  public function removeFields()
  {
    unset(
      $this['final_examination_subject_teacher_list'], $this['examination_subject_teacher_list'], $this['examination_repproved_subject_teacher_list'], $this['person_id'], $this['salary'], $this['aging_institution'], $this['file_number'] 
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

    $this->setWidget('is_active', new sfWidgetFormChoice(array('choices' => array('' => '', 1 => 'Sí', 0 => 'No'))));
    $this->setValidator('is_active', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $this->setWidget('subject', new sfWidgetFormPropelChoice(array('model' => 'Subject', 'add_empty' => true, 'order_by' => array('Name', 'asc'))));
    $this->setValidator('subject', new sfValidatorInteger(array('required'=>false)));

    //widgets options
    $this->getWidgetSchema()->setLabel('identification_number', 'Documento');
    $this->getWidgetSchema()->setHelp('lastname', 'Se filtrara por apellido del docente.');
    $this->getWidgetSchema()->setHelp('firstname', 'Se filtrara por nombre del docente.');
    $this->getWidgetSchema()->setHelp('identification_number', 'Se filtrara por numero de documento del docente.');
    $this->getWidgetSchema()->setHelp('subject', 'Se filtrara por materia que dicta el docente.');

    $this->validatorSchema->setOption('allow_extra_fields', true);

  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array('lastname' => 'Text', 'firstname' => 'Text', 'identification_number' => 'Text', 'is_active' => 'Boolean', 'subject' => 'Number'));

  }

  public function addLastnameColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = trim($value);
    if ($value != '')
    {
      $value = "%$value%";
      $criteria->setIgnoreCase(true);
      $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
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
      $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
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
      $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
      $criterion = $criteria->getNewCriterion(PersonPeer::IDENTIFICATION_NUMBER, $value, Criteria::LIKE);
      $criteria->add($criterion);
      $criteria->setDistinct();
    }

  }

  public function addIsActiveColumnCriteria(Criteria $criteria, $field, $value)
  {
    if ($value != '')
    {
      $c = new Criteria();
      $c->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
      $c->add(PersonPeer::IS_ACTIVE, 1);
      $c->clearSelectColumns();
      $c->addSelectColumn(PersonPeer::ID);
      $stmt = PersonPeer::doSelectStmt($c);
      $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

      if($value == '1')
      {
        $criteria->add(TeacherPeer::PERSON_ID, $ids, Criteria::IN);
      }
      else
      {
        $criteria->add(TeacherPeer::PERSON_ID, $ids, Criteria::NOT_IN);
      }
    }

  }

  public function addSubjectColumnCriteria(Criteria $criteria, $field, $value)
  {
    $current_career_subject_school_year_ids = CareerSubjectSchoolYearPeer::getCurrentCareerSubjectSchoolYearIdsBySubjectId($value);

    $criteria->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $current_career_subject_school_year_ids, Criteria::IN);
    $criteria->addJoin(CourseSubjectTeacherPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $criteria->addJoin(TeacherPeer::ID, CourseSubjectTeacherPeer::TEACHER_ID);

  }

}
