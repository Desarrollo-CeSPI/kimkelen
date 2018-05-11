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
    $csy=CareerSchoolYearPeer::retrieveByPk($csy_id);
    $career = $csy->getCareer();
    
    $status = array (StudentCareerSchoolYearStatus::WITHDRAWN, StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE);
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::ID, $csy_id);
    $c->add(StudentCareerSchoolYearPeer::STATUS, $status, Criteria::NOT_IN);
    $c->add(StudentCareerSchoolYearPeer::IS_PROCESSED, false);
    $c->setDistinct(StudentCareerSchoolYearPeer::YEAR);
    $c->clearSelectColumns();
    $c->addSelectColumn(StudentCareerSchoolYearPeer::YEAR);
    $stmt_y = StudentCareerSchoolYearPeer::doSelectStmt($c);
    $array_y = $stmt_y->fetchAll(PDO::FETCH_COLUMN);

    $years = array(''=>'');
    for($i=1;$i<=$career->getQuantityYears();$i++)
    {
        if(in_array($i, $array_y))
            $years[$i] = 'Año '.$i;
    }
    
    /*
    if(count($years) == $career->getQuantityYears() )
    {
        $csy->setIsProcessed(true);
        $csy->save();
    }
  */
   $this->setWidget('year',new sfWidgetFormChoice(array('choices' => $years)) );
   $this->setValidator('year', new sfValidatorString(array('required'=>true)));
   
  }

}