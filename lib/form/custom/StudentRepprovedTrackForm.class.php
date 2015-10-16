<?PHP
/**
 * StudentRepprovedTrackForm
 *
 * @author Ezequiel Gonzalez <egonzalez@cespi.unlp.edu.ar>
 */
class StudentRepprovedTrackForm extends SelectValuesForAttendanceDayForm
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    $this->getWidgetSchema()->setNameFormat('student_repproved_track[%s]');

    $this->configureWidgets();
    $this->configureValidators();

            $this->mergePostValidator(new sfValidatorCallback(array(
        'callback' => array($this, 'postValidateShift')
        )));

  }

  public function configureWidgets()
  {    

    $this->setWidget('career_school_year_id', new sfWidgetFormPropelChoice(array(
        'model' => 'CareerSchoolYear', 
        'add_empty' => true,
        'peer_method' => 'sort'
    )));

    $this->setWidget('shift_id', new sfWidgetFormPropelChoice(array(
        'model' => 'Shift', 
        'add_empty' => true
    )));

    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'student_repproved_track_career_school_year_id',
        "message_with_no_value" => "Seleccione una carrera y aparecerán los años que correspondan",
        'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));

    $division_widget = new sfWidgetFormPropelChoice(array('model' => 'Division', 'add_empty' => true));
    $this->setWidget('division_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $division_widget,
        'observe_widget_id' => 'student_repproved_track_year',
        "message_with_no_value" => "Seleccione una carrera y un año",
        'get_observed_value_callback' => array(get_class($this), 'getDivisions')
      )));

  }

  public function configureValidators()
  {
    $this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear', 'required' => true)));
    $this->setValidator('year', new sfValidatorString(array('required' => true)));
    $this->setValidator('division_id', new sfValidatorPropelChoice(array('required' => true, 'model' => 'Division')));
    $this->setValidator('shift_id', new sfValidatorPropelChoice(array('model' => 'Shift', 'required' => false)));
  
  }  

  public static function getYears($widget, $values)
  {
    $career = CareerSchoolYearPeer::retrieveByPk($values)->getCareer();
    $choices = $career->getYearsForOption(true);
    $widget->setOption('choices', $choices);
    sfContext::getInstance()->getUser()->setAttribute('career_school_year_id', $values);
  }

  public static function getDivisions($widget, $values)
  {
    $sf_user = sfContext::getInstance()->getUser();
    $career_school_year_id = $sf_user->getAttribute('career_school_year_id');

    $c = new Criteria();
    $c->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    $c->add(DivisionPeer::YEAR, $values);

    if ($sf_user->isPreceptor())
    {
      PersonalPeer::joinWithDivisions($c, $sf_user->getGuardUser()->getId());
    }

    $widget->setOption('criteria', $c);
  }
  
}

?>