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
    
    $status = array(StudentCareerSchoolYearStatus::WITHDRAWN, StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE);
    $c = new Criteria();
    $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $csy_id);
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
        //esta dentro del año a procesar y el año tiene todos los cursos cerrados
   
        $c = new Criteria();
        $c->add(CareerSchoolYearPeer::ID, $csy_id);
        $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
        $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
        $c->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
        $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
        $c->add(CoursePeer::SCHOOL_YEAR_ID, $csy->getSchoolYear()->getId());
        $c->add(CareerSubjectPeer::YEAR, $i);

        $courses_actives = CoursePeer::doCount($c);
        $c->add(CoursePeer::IS_CLOSED, true);
        
       
        if(in_array($i, $array_y) && $courses_actives == CoursePeer::doCount($c))
            $years[$i] = 'Año '.$i;
    
    }

   $this->setWidget('year',new sfWidgetFormChoice(array('choices' => $years)) );
   $this->setValidator('year', new sfValidatorString(array('required'=>true)));
   
  }

}