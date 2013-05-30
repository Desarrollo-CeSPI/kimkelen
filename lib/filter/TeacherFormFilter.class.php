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
      $this['final_examination_subject_teacher_list'], $this['examination_subject_teacher_list'], $this['examination_repproved_subject_teacher_list'], $this['person_id']
    );

  }

  public function configure()
  {
    $this->removeFields();

    //widgets
    $this->setWidget('person', new sfWidgetFormInput());
    $this->setValidator('person', new sfValidatorString(array('required' => false)));

    $this->setWidget('is_active', new sfWidgetFormChoice(array('choices' => array('' => '', 1 => 'Sí', 0 => 'No'))));
    $this->setValidator('is_active', new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))));

    $this->setWidget('career_school_year_id', new sfWidgetFormPropelChoice(array('model' => 'CareerSchoolYear', 'add_empty' => true)));
    $this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear', 'required' => false)));

    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'teacher_filters_career_school_year_id',
        "message_with_no_value" => "Seleccione una carrera y apareceran los años que correspondan",
        'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));
    $this->setValidator('year', new sfValidatorInteger(array('required' => false)));

    $courses_choices = new sfWidgetFormPropelChoice(array('model' => 'CareerSubjectSchoolYear', 'add_empty' => true));

    $this->setWidget('courses', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $courses_choices,
        'observe_widget_id' => 'teacher_filters_year',
        "message_with_no_value" => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getCourses')
      )));
    $this->setValidator('courses', new sfValidatorPass());


    $this->setWidget('subject', new sfWidgetFormPropelChoice(array('model' => 'Subject', 'add_empty' => true)));
    $this->setValidator('subject', new sfValidatorInteger(array('required'=>false)));


    //widgets options
    $this->getWidgetSchema()->setLabel('person', 'Que contenga el nombre');
    $this->getWidgetSchema()->setHelp('person', 'Se filtrarán todas las docentes que contengan lo ingresado en el nombre o apellido o numero de documento');

    $this->validatorSchema->setOption('allow_extra_fields', true);

  }

  public static function getYears($widget, $values)
  {
    $career = CareerSchoolYearPeer::retrieveByPk($values)->getCareer();
    $choices = $career->getYearsForOption(true);
    $widget->setOption('choices', $choices);
    sfContext::getInstance()->getUser()->setAttribute('career_school_year_id', $values);

  }

  public static function getCourses($widget, $values)
  {
    $career_school_year_id = sfContext::getInstance()->getUser()->getAttribute('career_school_year_id');
    $criteria = new Criteria();
    $criteria->add(CareerSubjectPeer::YEAR, $values);
    $criteria->add(CareerSubjectPeer::IS_OPTION, false);
    $criteria->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    $criteria->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $criteria->addJoin(CareerSubjectPeer::SUBJECT_ID, SubjectPeer::ID);
    $criteria->addAscendingOrderByColumn(SubjectPeer::NAME);
    $widget->setOption('criteria', $criteria);

  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array('person' => 'Text', 'career_school_year_id' => 'Number', 'year' => 'Number', 'courses' => 'Number', 'subject' => 'Number'));

  }

  public function addPersonColumnCriteria(Criteria $criteria, $field, $value)
  {
    $value = trim($value);
    if ($value != '')
    {
      $value = "%$value%";
      $criteria->setIgnoreCase(true);
      $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
      $criterion = $criteria->getNewCriterion(PersonPeer::FIRSTNAME, $value, Criteria::LIKE);
      $criterion->addOr($criteria->getNewCriterion(PersonPeer::LASTNAME, $value, Criteria::LIKE));
      $criterion->addOr($criteria->getNewCriterion(PersonPeer::IDENTIFICATION_NUMBER, $value, Criteria::LIKE));
      $criteria->add($criterion);
    }

  }

  public function addCareerSchoolYearIdColumnCriteria(Criteria $criteria, $field, $value)
  {
    $criteria->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
    $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID);
    $criteria->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $criteria->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $value);

  }

  public function addYearColumnCriteria(Criteria $criteria, $field, $value)
  {
    $criteria->add(CareerSubjectPeer::YEAR, $value);
    $criteria->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);

  }

  public function addCoursesColumnCriteria(Criteria $criteria, $field, $value)
  {
    $criteria->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $value);

  }

  public function addSubjectColumnCriteria(Criteria $criteria, $field, $value)
  {
    $current_career_subject_school_year_ids = CareerSubjectSchoolYearPeer::getCurrentCareerSubjectSchoolYearIdsBySubjectId($value);

    $criteria->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $current_career_subject_school_year_ids, Criteria::IN);
    $criteria->addJoin(CourseSubjectTeacherPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $criteria->addJoin(TeacherPeer::ID, CourseSubjectTeacherPeer::TEACHER_ID);

  }

}
