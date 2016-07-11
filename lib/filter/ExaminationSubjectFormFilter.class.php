<?php

/**
 * ExaminationSubject filter form.
 *
 * @package    sistema de alumnos
 * @subpackage filter
 * @author     Your name here
 */
class ExaminationSubjectFormFilter extends BaseExaminationSubjectFormFilter
{
  public function configure()
  {
    unset($this['examination_id'], $this['career_subject_school_year_id'],  $this['examination_subject_teacher_list']);

    $this->setWidget('is_closed', new sfWidgetFormChoice(array('choices' => array('' => '', 1 => 'Sí', 0 => 'No'))));
    $this->setValidator('is_closed', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $this->setWidget('subject', new sfWidgetFormFilterInput(array('with_empty' => false)));
    $this->setValidator('subject', new sfValidatorPass(array('required' => false)));

    $this->setWidget('year', new sfWidgetFormFilterInput(array('with_empty' => false)));
    $this->setValidator('year', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));

    $this->setWidget('career', new sfWidgetFormPropelChoice(array('model' => 'Career','add_empty' => true)));
    $this->setValidator('career', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Career', 'column' => 'id')));

    $this->setWidget('student', new dcWidgetFormPropelJQuerySearch(array('model' => 'Person', 'column' => array('lastname', 'firstname'), 'peer_method' => 'doSelectStudent')));
    $this->setValidator('student', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')));  
  }

  public function getFields()
  {
    return array(
      'is_closed' => 'Boolean', 
      'subject' => 'Text', 
      'year' => 'Number',
      'career' => 'ForeignKey',
      'student' => 'ForeignKey',
      );

  }

  public function addCareerColumnCriteria($criteria, $field, $value)
  { 
    if ($value !== null)
    {
      $criteria->addJoin(ExaminationSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
      $criteria->addJoin(CareerSchoolYearPeer::CAREER_ID, CareerPeer::ID);
      $criteria->add(CareerPeer::ID, $value);
    }
   
    $criteria->setDistinct(CoursePeer::ID); 
  }


  public function addSubjectColumnCriteria($criteria, $field, $value)
  {
    $value = trim($value['text']);
    if ($value != '')
    {
      $value = "%$value%";
      
      $criteria->setIgnoreCase(true);
      $criteria->addJoin(ExaminationSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
      $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
      $criteria->add(SubjectPeer::NAME, $value, Criteria::LIKE);
      $criteria->add(SubjectPeer::FANTASY_NAME, $value, Criteria::LIKE);
      
    }
  }

  public function addYearColumnCriteria($criteria, $field, $value)
  {
    if (! is_null($value['text']))
    {
      $criteria->addJoin(ExaminationSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
      $criteria->add(CareerSubjectPeer::YEAR, $value['text']);
    }
  }

  public function addStudentColumnCriteria(Criteria $criteria, $field, $value)
  {
    $criteria->addJoin(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, ExaminationSubjectPeer::ID);
    $criteria->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
    $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::ID, $value);

  }


}
