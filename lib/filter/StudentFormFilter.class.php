<?php

/**
 * Student filter form.
 *
 * @package    alumnos
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class StudentFormFilter extends BaseStudentFormFilter
{
  public function configure()
  {
    $this->unsetFields();

    $this->setWidget('student', new sfWidgetFormFilterInput(array('with_empty' => false)));
    $this->setValidator('student', new sfValidatorPass(array('required' => false)));
    $this->getWidgetSchema()->setHelp('student', 'Filtra por apellido o por número de documento');

    $max = CareerPeer::getMaxYear();
    $years = array('' => '');
    for ($i = 1; $i <= $max; $i++)
      $years[$i] = $i;

    $this->setWidget('year', new sfWidgetFormChoice(array('choices' => $years)));
    $this->setValidator('year' , new sfValidatorChoice(array('choices' => array_keys($years), 'required' => false)));

    $this->getWidgetSchema()->setHelp('year', 'El año filtra, de acuerdo al año que se encuentra cursando en el ciclo lectivo actual.');

    $user_criteria = $this->getDivisionCriteriaForUser(sfContext::getInstance()->getUser());
    $this->setWidget('division', new sfWidgetFormPropelChoice(array('model' => 'Division', 'criteria' => $user_criteria, 'add_empty' => true)));
    $this->setValidator('division', new sfValidatorPropelChoice(array('model' => 'Division', 'criteria' => $user_criteria, 'required' => false)));

    $this->setWidget('is_matriculated', new sfWidgetFormChoice(array('choices' => array('' => 'si o no', 1 => 'si', 0 => 'no'))));
    $this->setValidator('is_matriculated', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $this->setWidget('is_inscripted_in_career', new sfWidgetFormChoice(array('choices' => array('' => 'si o no', 1 => 'si', 0 => 'no'))));
    $this->setValidator('is_inscripted_in_career', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $this->setWidget('is_free_in_some_period', new sfWidgetFormChoice(array('choices' => array('' => 'si o no', 1 => 'si', 0 => 'no'))));
    $this->setValidator('is_free_in_some_period', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));
    $this->getWidgetSchema()->setLabel('is_free_in_some_period', 'Is free in some career school year period?');

    $status = StudentCareerSchoolYearStatus::getInstance('StudentCareerSchoolYearStatus');

    $this->setWidget('status', new sfWidgetFormChoice(array('choices' => $status->getOptions(true))));
    $this->setValidator('status', new sfValidatorChoice(array('choices' => $status->getKeys(), 'required' => false)));

    $this->widgetSchema->setHelp('status', 'This status is the status of the student in the current school year.');
    $this->getWidgetSchema()->setLabel('status', 'Current status');

    $this->setWidget('is_graduated', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_graduated', new sfValidatorBoolean());
    $this->widgetSchema->setHelp('is_graduated', 'If is checked, then will show only students graduated in some career.');

    $this->setWidget('disciplinary_sanction_count', new sfWidgetFormInput());
    $this->setValidator('disciplinary_sanction_count', new sfValidatorNumber(array('required' => false)));
    $this->widgetSchema->setHelp('disciplinary_sanction_count', 'Students that have more or equal to disciplinary sanctions in current school year.');
    
    $this->setWidget('health_info', new sfWidgetFormChoice(array('choices' => BaseCustomOptionsHolder::getInstance('HealthInfoStatus')->getOptions(true))));
    $this->setValidator('health_info', new sfValidatorChoice(array('choices' => BaseCustomOptionsHolder::getInstance('HealthInfoStatus')->getKeys(),'required' => false)));
    
  }

  public function unsetFields()
  {
    unset(
      $this['global_file_number'],
      $this['person_id'],
      $this['folio_number'],
      $this['order_of_merit'],
      $this['occupation_id'],
      $this['busy_starts_at'],
      $this['busy_ends_at'],
      $this['student_career_subject_allowed_list'],
      $this['blood_group'],
      $this['blood_factor'],
      $this['emergency_information'],
      $this['health_coverage_id'],
      $this['order_of_merit'],
      $this['folio_number'],
      $this['origin_school_id'],
      $this['educational_dependency'],
      $this['student_tag_list']
    );
  }

  private function getDivisionCriteriaForUser($user)
  {
    $criteria = new Criteria();
    $school_year = SchoolYearPeer::retrieveCurrent();
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $criteria->addJoin(CareerSchoolYearPeer::ID, DivisionPeer::CAREER_SCHOOL_YEAR_ID);
    if($user->isPreceptor())
    {
      AdminGeneratorFiltersClass::addDivisionPreceptorCriteria($criteria, $user);
    }

    if($user->isTeacher())
    {
      AdminGeneratorFiltersClass::addDivisionTeacherCriteria($criteria, $user);
    }

    $criteria->addAscendingOrderByColumn(DivisionPeer::YEAR);
    $criteria->addAscendingOrderByColumn(DivisionPeer::DIVISION_TITLE_ID);

    return $criteria;
  }

  public function addIsInscriptedInCareerColumnCriteria($criteria, $field, $value)
  {
    if ($value != '')
    {
      $c = new Criteria();
      $c->addJoin(CareerStudentPeer::STUDENT_ID, StudentPeer::ID);
      $c->clearSelectColumns();
      $c->addSelectColumn(StudentPeer::ID);
      $stmt = StudentPeer::doSelectStmt($c);
      $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

      if($value == '1')
      {
        $criteria->add(StudentPeer::ID, $ids, Criteria::IN);
      }
      else
      {
        $criteria->add(StudentPeer::ID, $ids, Criteria::NOT_IN);
      }
    }
  }

  public function addIsMatriculatedColumnCriteria($criteria, $field, $value)
  {
    if ($value != '')
    {
      $c = new Criteria();
      $c->add(SchoolYearPeer::ID , SchoolYearPeer::retrieveCurrent()->getId());
      $c->addJoin(SchoolYearPeer::ID, SchoolYearStudentPeer::SCHOOL_YEAR_ID);
      $c->addJoin(StudentPeer::ID, SchoolYearStudentPeer::STUDENT_ID);
      $c->clearSelectColumns();
      $c->addSelectColumn(StudentPeer::ID);
      $stmt = StudentPeer::doSelectStmt($c);
      $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

      if($value == '1')
      {
        $criteria->add(StudentPeer::ID, $ids, Criteria::IN);
      }
      else
      {
        $criteria->add(StudentPeer::ID, $ids, Criteria::NOT_IN);
      }
    }
  }

  public function addIsFreeInSomePeriodColumnCriteria($criteria, $field, $value)
  {
    if ($value != '')
    {
      $criteria->addJoin(StudentFreePeer::STUDENT_ID , StudentPeer::ID);
      $criteria->add(StudentFreePeer::IS_FREE , $value);
      $criteria->addAnd(StudentFreePeer::CAREER_SCHOOL_YEAR_PERIOD_ID, CareerSchoolYearPeriodPeer::retrieveCurrentPeriodsIds(), Criteria::IN);
    }
  }

  public function getFields()
  {
    return array_merge(parent::getFields(),
      array(
        'student' => 'Text',
        'year' => 'Number',
        'division' => 'Number',
        'is_matriculated' => 'Boolean',
        'is_inscripted_in_career' => 'Boolean',
        'is_free_in_some_period' => 'Boolean',
        'is_graduated' => 'Boolean',
        'disciplinary_sanction_count' => 'Number',
        'status' => 'Number',
        'health_info' => 'Text'));
  }

  public function addIsGraduatedColumnCriteria(Criteria $criteria, $field, $values)
  {
    if ($values)
    {
      $criteria->addJoin(CareerStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
      $criteria->add(CareerStudentPeer::STATUS, CareerStudentStatus::GRADUATE);
    }
  }

  public function addDisciplinarySanctionCountColumnCriteria(Criteria $criteria, $field, $values)
  {
    if ($values)
    {
      $criteria->addJoin(StudentPeer::ID, StudentDisciplinarySanctionPeer::STUDENT_ID, Criteria::INNER_JOIN);
      $criteria->add(StudentDisciplinarySanctionPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
      $criteria->addGroupByColumn(StudentDisciplinarySanctionPeer::STUDENT_ID);
      $criterion = $criteria->getNewCriterion(StudentDisciplinarySanctionPeer::STUDENT_ID, 'count('.StudentDisciplinarySanctionPeer::STUDENT_ID.') >='.$values, Criteria::CUSTOM);
      $criteria->addHaving($criterion);
    }
  }

  public function addStatusColumnCriteria(Criteria $criteria, $field, $values)
  {
    $criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
	
	//si $values es Repetidor del año pasado pero cursando año lectivo actual
	if($values == StudentCareerSchoolYearStatus::LAST_YEAR_REPPROVED)
	{
		//chequea que en el año anterior tenga estado REPPROVED y para este año tenga matricula
		$current_school_year = SchoolYearPeer::retrieveCurrent();
		$school_year = SchoolYearPeer::retrieveLastYearSchoolYear($current_school_year);
		$criteria->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID,CareerSchoolYearPeer::ID);
		$criteria->addJoin(SchoolYearStudentPeer::STUDENT_ID,StudentPeer::ID);
		$criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID,$school_year->getId());
		$criteria->add(StudentCareerSchoolYearPeer::STATUS,StudentCareerSchoolYearStatus::REPPROVED);
		$criteria->add(SchoolYearStudentPeer::SCHOOL_YEAR_ID, $current_school_year->getId());
		
	}else
	{
	 $criteria->add(StudentCareerSchoolYearPeer::STATUS, $values);
	}
    
   
  }

  /**
   * This method filters by Lastname or DNI of person.
   *
   * @param Criteria $criteria
   * @param string $field
   * @param array $values
   */
  public function addStudentColumnCriteria(Criteria $criteria, $field, $values)
  {
    $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $criterion = $criteria->getNewCriterion(PersonPeer::IDENTIFICATION_NUMBER, $values['text'], Criteria::LIKE);
    $criterion->addOr($criteria->getNewCriterion(PersonPeer::LASTNAME, "%" . $values['text'] . "%", Criteria::LIKE));
    $criteria->add($criterion);

    $criteria->setDistinct();

  }

  public function addYearColumnCriteria(Criteria $criteria , $field, $values)
  {
    if ($values)
    {
      $criteria->add(StudentCareerSchoolYearPeer::YEAR, $values);
      $criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
      $criteria->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
      $criteria->addJoin(CareerSchoolYearPeer::CAREER_ID, CareerPeer::ID);
      $criteria->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    }
  }

  public function addDivisionColumnCriteria(Criteria $criteria , $field, $values)
  {
    if ($values)
    {
      $criteria->add(DivisionStudentPeer::DIVISION_ID, $values);
      $criteria->addJoin(DivisionStudentPeer::STUDENT_ID, StudentPeer::ID);
    }
  }
  
  public function addHealthInfoColumnCriteria(Criteria $criteria , $field, $values)
  {
    if($values)
    {
		$criteria->addJoin(StudentPeer::ID,SchoolYearStudentPeer::STUDENT_ID);
		$criteria->add(SchoolYearStudentPeer::HEALTH_INFO, $values);
				
	}
  }
}
