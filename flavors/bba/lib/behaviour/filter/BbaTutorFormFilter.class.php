<?php

/**
 * Tutor filter form.
 *
 * @package    conservatorio
 * @subpackage filter
 * @author     Desarrollo CeSPI
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BbaTutorFormFilter extends TutorFormFilter
{

  public function configure()
  {   
    parent::configure();
    
    $c_criteria = new Criteria(CareerPeer::DATABASE_NAME);
    
    $this->setWidget('career', new sfWidgetFormPropelChoice(array('model' => 'Career', 'criteria' => $c_criteria, 'add_empty' => true)));
    $this->setValidator('career', new sfValidatorPropelChoice(array('model' => 'Career', 'criteria' => $c_criteria, 'required' => false)));
	
    $w = new sfWidgetFormChoice(array('choices' => array()));
    
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'tutor_filters_career',
        'message_with_no_value' => 'Seleccione una carrera',
        'get_observed_value_callback' => array(get_class($this), 'getYears')
      )));
      
    $this->getWidgetSchema()->setHelp('year', 'El año filtra de acuerdo al año en el que se encuentra cursando el alumno.');
    $this->getWidgetSchema()->moveField('career', sfWidgetFormSchema::BEFORE, 'year');
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array('career' => 'Number', 'year' => 'Number'));

  }
  
  public static function getYears($widget, $values){
	
	$career = CareerPeer::retrievebyPk($values);
	$max = $career->getMaxYear();

	$years = array('' => '');
        for ($i = 1; $i <= $max; $i++)
            $years[$i] = $i;
        $widget->setOption('choices', $years);
  }
  
  public function addCareerColumnCriteria(Criteria $criteria , $field, $values)
  {
    if ($values)
    {
        $criteria->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
        $criteria->add(CareerSchoolYearPeer::CAREER_ID, $values);
        $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
        $criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
        $criteria->addJoin(StudentPeer::ID, StudentTutorPeer::STUDENT_ID);
        $criteria->addJoin(StudentTutorPeer::TUTOR_ID, TutorPeer::ID);
    }
  }
}
