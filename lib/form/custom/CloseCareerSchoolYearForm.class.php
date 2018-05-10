<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CloseCareerSchoolYearForm extends sfForm
{

  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    $this->widgetSchema->setNameFormat('close_career_school_year[%s]');
    $this->validatorSchema->setOption("allow_extra_fields", true);

    $csy_id = sfContext::getInstance()->getRequest()->getParameter('id');
    $career = CareerSchoolYearPeer::retrieveByPk($csy_id)->getCareer();
    
    
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::ID, $csy_id);
    $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->addJoin(StudentPeer::ID, StudentCareerSchoolYearPeer::STUDENT_ID);
    //$c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::IN_COURSE);
    $c->add(StudentCareerSchoolYearPeer::IS_PROCESSED, true);
    $c->setDistinct(StudentCareerSchoolYearPeer::YEAR);
    $c->clearSelectColumns();
    $c->addSelectColumn(StudentCareerSchoolYearPeer::YEAR);
    $stmt_y = StudentCareerSchoolYearPeer::doSelectStmt($c);
    $array_y = $stmt_y->fetchAll(PDO::FETCH_COLUMN);
    
    /*$c = new Criteria();
    $c->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID,$csy_id);
    $c->setDistinct(DivisionPeer::YEAR);
    $c->clearSelectColumns();
    $c->addSelectColumn(DivisionPeer::YEAR);
    
    $stmt_y = DivisionPeer::doSelectStmt($c);
    $array_y = $stmt_y->fetchAll(PDO::FETCH_COLUMN);
    */
 
    $years = array(''=>'');
    for($i=1;$i<=$career->getQuantityYears();$i++)
    {
        if(! in_array($i, $array_y))
            $years[$i] = 'Año '.$i;
    }
  
   $this->setWidget('year',new sfWidgetFormChoice(array('choices' => $years)) );
   $this->setValidator('year', new sfValidatorString(array('required'=>true)));
   
  }
  
  public function save()
  {
    $csy = CareerSchoolYearPeer::retrieveByPK($this->getOption('career_school_year_id'));
    $year = $this->getValue('year'); 
    $csy->createLastYearDivisions($year);
  }

}