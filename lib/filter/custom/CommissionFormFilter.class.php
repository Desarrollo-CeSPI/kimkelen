<?php

/**
 * Course filter form.
 *
 * @package    conservatorio
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class CommissionFormFilter extends BaseCourseFormFilter
{
  public function configure()
  {
    unset($this['starts_at'], $this['quota'], $this['division_id'], $this['related_division_id'], $this['is_pathway']);

    $this->getWidget('name')->setOption('with_empty', false);
    
    $this->setWidget('subject', new sfWidgetFormFilterInput());
    $this->getWidget('subject')->setOption('with_empty', false);
    $this->setValidator('subject', new sfValidatorPass(array('required' => false)));

	  $this->setWidget('classroom_id', new sfWidgetFormPropelChoice(array('model' => 'Classroom', 'add_empty' => true)));
	  $this->setValidator('classroom_id', new sfValidatorPass(array('required' => false)));
	  
    $this->setWidget('year', new sfWidgetFormFilterInput(array('with_empty' => false)));
    $this->setValidator('year', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));

    $this->setWidget('is_closed', new sfWidgetFormChoice(array('choices' => array('' => '', 1 => 'Sí', 0 => 'No'))));
    $this->setValidator('is_closed', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $this->setWidget('school_year_id', new sfWidgetFormPropelChoice(array('model' => 'SchoolYear', 'add_empty' => false)));
    $this->setValidator('school_year_id', new sfValidatorPropelChoice(array('required' => false, 'model' => 'SchoolYear', 'column' => 'id')));

    $this->setWidget('current_period', new sfWidgetFormFilterInput(array('with_empty' => false)));
    $this->setValidator('current_period', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));

    $this->setWidget('career_school_year', new sfWidgetFormPropelChoice(array('model' => 'CareerSchoolYear', 'criteria' => $this->getCareersCriteria(), 'add_empty' => true)));
    $this->setValidator('career_school_year', new sfValidatorPropelChoice(array('required' => false, 'model' => 'CareerSchoolYear', 'column' => 'id')));

    $this->setWidget('student', new dcWidgetFormPropelJQuerySearch(array('model' => 'Person', 'column' => array('lastname', 'firstname'), 'peer_method' => 'doSelectStudent')));
    $this->setValidator('student', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')));

    $this->setWidget('teacher', new dcWidgetFormPropelJQuerySearch(array('model' => 'Person', 'column' => array('lastname', 'firstname'), 'peer_method' => 'doSelectTeacher')));
    $this->setValidator('teacher', new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')));

    $this->getWidgetSchema()->setLabel('classroom_id', 'Aula');
    $this->getWidgetSchema()->setLabel('current_period', 'Período');
    $this->getWidgetSchema()->setLabel('career_school_year', 'Carrera');
    $this->getWidgetSchema()->setHelp('name', 'Se filtrara por nombre de la comisión.');
    $this->getWidgetSchema()->setHelp('subject', 'Se filtrara por materias que contiene la comisión.');
    $this->getWidgetSchema()->setHelp('career_school_year', 'Seleccione alguna de las carreras habilitadas');
    $this->getWidgetSchema()->setHelp('current_period', 'Se filtrara por el periodo actual de la comisión.');
    $this->getWidgetSchema()->setHelp('year', 'Se filtrara por el año de la materia que contiene la comisión.');
  }

  public static function getCareersCriteria()
  {
    $criteria = new Criteria();
    $school_year = SchoolYearPeer::retrieveCurrent();

    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());

    return $criteria;
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
        'name' => 'Text',
        'subject' => 'Text',
        'year' => 'Number',
        'is_closed' => 'Boolean',
        'career_school_year' => 'ForeignKey',
        'current_period' => 'Number',
        'school_year_id' => 'ForeignKey',
	      'classroom_id' => 'ForeignKey',
        'teacher' => 'ForeignKey',
        'student' => 'ForeignKey'));

  }

  public function addCareerSchoolYearColumnCriteria($criteria, $field, $value)
  {
    if ($value !== null)
    {
      $criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID, Criteria::INNER_JOIN);
      $criteria->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID, Criteria::INNER_JOIN);
      $criteria->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $value);
    }

    $criteria->setDistinct(CoursePeer::ID);
  }

  public function addStudentColumnCriteria(Criteria $criteria, $field, $value)
  {
    if ($value !== null)
    {
      $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
      $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
      $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
      $criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
      $criteria->add(PersonPeer::ID,$value);
    }
  }

  public function addTeacherColumnCriteria($criteria, $field, $value)
  {
    if ($value !== null)
    {
      $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
      $criteria->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
      $criteria->addJoin(CourseSubjectTeacherPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
      $criteria->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
      $criteria->add(PersonPeer::ID,$value);
    }
  }

  public function addYearColumnCriteria($criteria, $field, $value)
  {
    if ($value['text'] != '')
    {
      $criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
      $criteria->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
      $criteria->add(CareerSubjectPeer::YEAR, $value['text']);
    }
  }

  public function addNameColumnCriteria($criteria, $field, $value)
  {
    if ($value['text'] != '')
    {
      $value = $value['text'];

      $criteria->add(CoursePeer::NAME, "%$value%", Criteria::LIKE);

    }

    $criteria->setDistinct(CoursePeer::ID);
  }

  public function addSubjectColumnCriteria($criteria, $field, $value)
  {
    if ($value['text'] != '')
    {
      $value = $value['text'];
      $criteria->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
      $criteria->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
      $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID, Criteria::INNER_JOIN);

      $criterion = $criteria->getNewCriterion(SubjectPeer::NAME, "%$value%", Criteria::LIKE);

      $criteria->addOr($criterion);
      $criteria->setDistinct();
    }
  }

  public function addSchoolYearIdColumnCriteria($criteria, $field, $value)
  {
    if ($value !== null)
    {
      $criteria->add(SchoolYearPeer::ID, $value);
      $criteria->addJoin(SchoolYearPeer::ID, CoursePeer::SCHOOL_YEAR_ID, Criteria::INNER_JOIN);
    }
  }

	public function addClassroomIdColumnCriteria($criteria, $field, $value)
	{
		$criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
		$criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectDayPeer::COURSE_SUBJECT_ID, Criteria::INNER_JOIN);
		$criteria->addJoin(CourseSubjectDayPeer::CLASSROOM_ID, ClassroomPeer::ID, Criteria::INNER_JOIN);
		$criteria->add(ClassroomPeer::ID, $value);



	}
}