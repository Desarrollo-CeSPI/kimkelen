<?php

/**
 * DivisionRatingReport filter form.
 *
 * @package    kimkelen
 * @subpackage filter
 * @author     gramirez
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class SchoolYearAverageReportFormFilter extends sfFormFilter
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    $this->getWidgetSchema()->setNameFormat('average_report[%s]');

    $this->configureWidgets();
    $this->configureValidators();
  }

  public function configureWidgets()
  {    
    $this->setWidget('career_school_year_id', new sfWidgetFormPropelChoice(array(
      'model' => 'CareerSchoolYear', 
      'add_empty' => true,
      
    )));    
    
    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'average_report_career_school_year_id',
        "message_with_no_value" => "Seleccione una carrera y aparecerán los años que correspondan",
        'get_observed_value_callback' => array(get_class($this), 'getYears')
    )));
  }

  public function configureValidators()
  {
    $this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear')));
    $this->setValidator('year', new sfValidatorString(array()));
  }  

  public static function getYears($widget, $values)
  {
    $career = CareerSchoolYearPeer::retrieveByPk($values)->getCareer();
    $choices = $career->getYearsForOption(true);
    $widget->setOption('choices', $choices);

  }
 
}