<?php

/**
 * StatsStudentFormFIlter
 *
 * @author María Emilia Corrons <ecorrons@cespi.unlp.edu.ar>
 */
class StatsStudentFormFilter extends StudentFormFilter
{
  public function configure()
  {
    $this->unsetFields();

    $user_criteria = $this->getDivisionCriteriaForUser(sfContext::getInstance()->getUser());
    $this->setWidget('division', new sfWidgetFormPropelChoice(array('model' => 'Division', 'criteria' => $user_criteria, 'add_empty' => true)));
    $this->setValidator('division', new sfValidatorPropelChoice(array('model' => 'Division', 'criteria' => $user_criteria, 'required' => false)));

    $this->setWidget('shift', new sfWidgetFormPropelChoice(array('model' => 'Shift', 'add_empty' => true)));
    $this->setValidator('shift', new sfValidatorPropelChoice(array('model' => 'Shift', 'required' => false)));

    $this->setWidget('school_year', new sfWidgetFormPropelChoice(array('model' => 'SchoolYear', 'add_empty' => true)));
    $this->setValidator('school_year', new sfValidatorPropelChoice(array('model' => 'SchoolYear', 'required' => false)));

    $this->setWidget('career_school_year', new sfWidgetFormPropelChoice(array('model' => 'CareerSchoolYear', 'add_empty' => true)));
    $this->setValidator('career_school_year', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear', 'required' => false)));

    /*   $w = new sfWidgetFormChoice(array('choices' => array()));
      $this->setWidget('year', new dcWidgetAjaxDependence(array(
      'dependant_widget' => $w,
      'observe_widget_id' => 'student_filters_career_school_year',
      "message_with_no_value" => "Seleccione una carrera y apareceran los años que correspondan",
      'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));

      $this->setValidator('year', new sfValidatorString(array('required' => false)));
     * */

    $this->setWidget('is_graduated', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_graduated', new sfValidatorBoolean());

    $this->setWidget('is_entrant', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_entrant', new sfValidatorBoolean());

    $this->setWidget('has_disciplinary_sanctions', new sfWidgetFormInputCheckbox());
    $this->setValidator('has_disciplinary_sanctions', new sfValidatorBoolean());
  }

  public function unsetFields()
  {
    unset(
      $this['global_file_number'], $this['person_id'], $this['student'], $this['folio_number'], $this['order_of_merit'], $this['occupation_id'], $this['busy_starts_at'], $this['busy_ends_at'], $this['student_career_subject_allowed_list'], $this['blood_group'], $this['blood_factor'], $this['emergency_information'], $this['health_coverage_id'], $this['order_of_merit'], $this['folio_number'], $this['student_tag_list'], $this['status'], $this['is_matriculated'], $this['is_inscripted_in_career']
    );
  }

  private function getDivisionCriteriaForUser($user)
  {
    $criteria = new Criteria();
    $filters = $user->getAttribute('student_stats.filters', null, 'admin_module');
    $csy = CareerSchoolYearPeer::retrieveByPK($user->getAttribute('career_school_year'));
    $csy ? $school_year = $csy->getSchoolYear() : $school_year = SchoolYearPeer::retrieveByPK($filters['school_year']);
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $criteria->addJoin(CareerSchoolYearPeer::ID, DivisionPeer::CAREER_SCHOOL_YEAR_ID);
    if ($user->isPreceptor())
    {
      AdminGeneratorFiltersClass::addDivisionPreceptorCriteria($criteria, $user);
    }

    /* if($user->isTeacher())
      {
      AdminGeneratorFiltersClass::addDivisionTeacherCriteria($criteria, $user);
      }
     */
    return $criteria;
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
        'division' => 'Number',
        'shift' => 'Number',
        'school_year' => 'Number',
        'career_school_year' => 'Number',
        'year' => 'Number',
        'is_graduated' => 'Boolean',
        'is_entrant' => 'Boolean',
        'has_disciplinary_sanctions' => 'Boolean'));
  }

  public function addIsEntrantColumnCriteria(Criteria $criteria, $field, $values)
  {
    if ($values)
    {
      $user = sfContext::getInstance()->getUser();
      $filters = $user->getAttribute('student_stats.filters', null, 'admin_module');
      $school_year = SchoolYearPeer::retrieveByPK($filters['school_year']);
      $last_year_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($school_year);
      if ($last_year_school_year)
      {
        $criteria->addJoin(SchoolYearStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
        $criteria->add(SchoolYearStudentPeer::STUDENT_ID, SchoolYearStudentPeer::retrieveStudentIdsForSchoolYear($last_year_school_year), Criteria::NOT_IN);
        $criteria->addAnd(SchoolYearStudentPeer::STUDENT_ID, SchoolYearStudentPeer::retrieveStudentIdsForSchoolYear($school_year), Criteria::IN);
      }
    }
  }

  public function addHasDisciplinarySanctionsColumnCriteria(Criteria $criteria, $field, $values)
  {
    if ($values)
    {
      $user = sfContext::getInstance()->getUser();
      $filters = $user->getAttribute('student_stats.filters', null, 'admin_module');
      $school_year = SchoolYearPeer::retrieveByPK($filters['school_year']);

      $criteria->addJoin(StudentDisciplinarySanctionPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
      $criteria->add(StudentDisciplinarySanctionPeer::SCHOOL_YEAR_ID, $school_year->getId());
    }
  }

  public function addYearColumnCriteria(Criteria $criteria, $field, $values)
  {
    if ($values)
    {
      $criteria->addJoin(DivisionStudentPeer::STUDENT_ID, StudentPeer::ID);
      $criteria->addJoin(DivisionStudentPeer::DIVISION_ID, DivisionPeer::ID);
      $criteria->add(DivisionPeer::YEAR, $values);
      //$criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
      //$criteria->add(StudentCareerSchoolYearPeer::YEAR, $values);
    }
  }

  public function addShiftColumnCriteria(Criteria $criteria, $field, $values)
  {
    if ($values)
    {
      $user = sfContext::getInstance()->getUser();
      $filters = $user->getAttribute('student_stats.filters', null, 'admin_module');
      $shift = ShiftPeer::retrieveByPk($values);

      if (isset($filters['year']))
      {
        $ids = $shift->getStudentIdsFromDivisions(DivisionStudentPeer::doSelectForCareerSchoolYearShiftAndYear(CareerSchoolYearPeer::retrieveByPk($filters['career_school_year']), $shift, $filters['year']));
      }
      else
      {
        $ids = $shift->getStudentIdsFromDivisions(DivisionStudentPeer::doSelectDivisionsForCareerSchoolYearAndShift(CareerSchoolYearPeer::retrieveByPk($filters['career_school_year']), $shift));
      }
      $criteria->add(StudentPeer::ID, $ids, Criteria::IN);

      $criteria->setDistinct();
    }
  }

  public function addSchoolYearColumnCriteria(Criteria $criteria, $field, $values)
  {
    if ($values)
    {
      $c = new Criteria();
      $c->add(SchoolYearPeer::ID, $values);
      $c->addJoin(SchoolYearPeer::ID, SchoolYearStudentPeer::SCHOOL_YEAR_ID);
      $c->addJoin(StudentPeer::ID, SchoolYearStudentPeer::STUDENT_ID);
      $c->clearSelectColumns();
      $c->addSelectColumn(StudentPeer::ID);
      $stmt = StudentPeer::doSelectStmt($c);
      $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
      $criteria->add(StudentPeer::ID, $ids, Criteria::IN);
    }
  }

  public function addCareerSchoolYearColumnCriteria(Criteria $criteria, $field, $values)
  {
    if ($values)
    {
      $criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
      $criteria->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $values);
    }
  }

  public static function getYears($widget, $values)
  {
    $career = CareerSchoolYearPeer::retrieveByPk($values)->getCareer();
    $choices = $career->getYearsForOption(true);
    $widget->setOption('choices', $choices);
  }

  public function addIsGraduatedColumnCriteria(Criteria $criteria, $field, $values)
  {
    if ($values)
    {
      $user = sfContext::getInstance()->getUser();
      $filters = $user->getAttribute('student_stats.filters', null, 'admin_module');
      $career_school_year = CareerSchoolYearPeer::retrieveByPK($filters['career_school_year']);
      $criteria->addJoin(CareerStudentPeer::STUDENT_ID, StudentPeer::ID);
      $criteria->add(CareerStudentPeer::CAREER_ID, $career_school_year->getCareer()->getId());
      $criteria->addAnd(CareerStudentPeer::STATUS, CareerStudentStatus::GRADUATE);
    }
  }

}
