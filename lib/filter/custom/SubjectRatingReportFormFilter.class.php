<?php

/**
 * RatingReport filter form.
 *
 * @package    kimkelen
 * @subpackage filter
 * @author     gramirez
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class SubjectRatingReportFormFilter extends sfFormFilter
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    $this->getWidgetSchema()->setNameFormat('subject_rating_report[%s]');

    $this->configureWidgets();
    $this->configureValidators();
  }

  public function configureWidgets()
  {    
    $this->setWidget('career_school_year_id', new sfWidgetFormPropelChoice(array(
      'model' => 'CareerSchoolYear', 
      'add_empty' => true,
      'peer_method' => 'sort'
    )));    
    
    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'subject_rating_report_career_school_year_id',
        "message_with_no_value" => "Seleccione una carrera y aparecerán los años que correspondan",
        'get_observed_value_callback' => array(get_class($this), 'getYears')
    )));

    $course_subject_widget = new sfWidgetFormPropelChoice(array('model' => 'CourseSubject', 'add_empty' => true, 'method' => "FullToString",));

    $this->setWidget('course_subject_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $course_subject_widget,
        'observe_widget_id' => 'subject_rating_report_year',
        "message_with_no_value" => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getCourseSubjects')
      )));
  }

  public function configureValidators()
  {
  	$this->setValidator('course_subject_id', new sfValidatorPropelChoice(array('required' => true, 'model' => 'CourseSubject')));
    $this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear')));
    $this->setValidator('year', new sfValidatorString(array()));
  }  

  public static function getYears($widget, $values)
  {
    $career = CareerSchoolYearPeer::retrieveByPk($values)->getCareer();
    $choices = $career->getYearsForOption(true);
    $widget->setOption('choices', $choices);
    sfContext::getInstance()->getUser()->setAttribute('career_school_year_id', $values);
  }

  public static function getCourseSUbjects($widget, $values)
  {
    $sf_user = sfContext::getInstance()->getUser();
    $career_school_year = CareerSchoolYearPeer::retrieveByPK(sfContext::getInstance()->getUser()->getAttribute('career_school_year_id'));

    $c = new Criteria();

    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $c->addJoin(CourseSubjectPeer::COURSE_ID,  CoursePeer::ID);    
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(CareerSubjectPeer::YEAR, $values);

    if ($sf_user->isPreceptor())
    {
      $course_ids = PersonalPeer::retrieveCourseIdsjoinWithDivisionCourseOrCommission($sf_user->getGuardUser()->getId(), true);
      $c->add(CoursePeer::ID, $course_ids, Criteria::IN);

      $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    }

    $widget->setOption('criteria', $c);
  }
}