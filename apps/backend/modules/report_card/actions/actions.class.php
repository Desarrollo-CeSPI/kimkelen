<?php
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php

/**
 * report_card actions.
 *
 * @package    sistema de alumnos
 * @subpackage report_card
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class report_cardActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('division_student_id', null);
    $this->getUser()->setAttribute('student_id', null);
    $this->division = DivisionPeer::retrieveByPK($this->getUser()->getReferenceFor('division'));
    $this->career_id = $this->division->getCareer()->getId();
    $this->students = $this->division->getStudents();
    $this->back_url= '@division';
    $this->setLayout('cleanLayout');
  }

  public function executeReportCardsToPDF(sfWebRequest $request)
  {
    $division_student_id = $this->getUser()->getAttribute('division_student_id');
   
    
    if ($division_student_id == null)
        $this->division = DivisionPeer::retrieveByPK($this->getUser()->getReferenceFor('division'));
    else{

        $this->division = DivisionPeer::retrieveByPK($division_student_id);
        $this->students = array(StudentPeer::retrieveByPK ($this->getUser()->getAttribute('student_id')));
        
        $this->getUser()->setAttribute('division_student_id', null);
        $this->getUser()->setAttribute('student_id', null);
        
    }
    
    if (is_null($this->division))
    {
        $this->division = DivisionPeer::retrieveByPk($this->getUser()->getAttribute('division_id'));
    }
    if (is_null($this->students))
        $this->students = $this->division->getStudents();
    
    $this->career_id = $this->division->getCareer()->getId();

    $this->setLayout('cleanLayout');
    $this->setTemplate('index');
  }

  public function executeSubsetReportCardsToPDF(sfWebRequest $request)
  {
      
    $this->division = DivisionPeer::retrieveByPK($this->getUser()->getReferenceFor('division'));

    if (is_null($this->division))
    {
      $this->division = DivisionPeer::retrieveByPk($this->getUser()->getAttribute('division_id'));
    }
    if ($request->getParameter('all_approved'))
    {
      $this->students = $this->division->getStudentsWithAllSubjectsApproved();
    }
    else
    {
      $this->students = $this->division->getStudentsWithDisapprovedSubjects();
    }
    $this->career_id = $this->division->getCareer()->getId();

    $this->setLayout('cleanLayout');
    $this->setTemplate('index');
  }

  public function executePrintStudent(sfWebRequest $request)
  {
      
      
    $this->student_career_school_year = StudentCareerSchoolYearPeer::retrieveByPK($request->getParameter('student_career_school_year_id'));
    
    $this->students = array($this->student_career_school_year->getStudent());
    $this->career_id = $this->student_career_school_year->getCareerSchoolYear()->getCareerId();
    $this->division = DivisionPeer::retrieveByStudentCareerSchoolYear($this->student_career_school_year);
    
       
    $this->getUser()->setAttribute('division_id', $this->division->getId());
    $this->getUser()->setAttribute('division_student_id', $this->division->getId());
    $this->getUser()->setAttribute('student_id', $this->student_career_school_year->getStudent()->getId());
    
    
 
    $this->back_url = '@student';

    $this->setLayout('cleanLayout');
    $this->setTemplate('index');
  }

   public function executePrintObservationsCard(sfWebRequest $request)
   {
        $this->students = array(StudentPeer::retrieveByPk($request->getParameter('student_id')));
	$this->setLayout('cleanLayout');
   }
   
    public function executePrintStudentObservationsCard(sfWebRequest $request)
    {
        $this->student_career_school_year = StudentCareerSchoolYearPeer::retrieveByPK($request->getParameter('student_career_school_year_id'));
        $this->students = array($this->student_career_school_year->getStudent());
        $this->career_id = $this->student_career_school_year->getCareerSchoolYear()->getCareerId();
        $this->division = DivisionPeer::retrieveByStudentCareerSchoolYear($this->student_career_school_year);
        $this->observations = ObservationMarkPeer::doSelect(new Criteria());

        $this->getUser()->setAttribute('division_id', $this->division->getId());
        $this->getUser()->setAttribute('division_student_id', $this->division->getId());
        $this->getUser()->setAttribute('student_id', $this->student_career_school_year->getStudent()->getId());
        

        $this->back_url = '@student';
        $this->setLayout('cleanLayout');
    }
   
  public function executeObservationsCardsToPDF(sfWebRequest $request)
    {
      $division_student_id = $this->getUser()->getAttribute('division_student_id');


      if ($division_student_id == null)
          $this->division = DivisionPeer::retrieveByPK($this->getUser()->getReferenceFor('division'));
      else{

          $this->division = DivisionPeer::retrieveByPK($division_student_id);
          $this->students = array(StudentPeer::retrieveByPK ($this->getUser()->getAttribute('student_id')));

          $this->getUser()->setAttribute('division_student_id', null);
          $this->getUser()->setAttribute('student_id', null);

      }

      if (is_null($this->division))
      {
          $this->division = DivisionPeer::retrieveByPk($this->getUser()->getAttribute('division_id'));
      }
      if (is_null($this->students))
          $this->students = $this->division->getStudents();

      $this->career_id = $this->division->getCareer()->getId();
      $this->observations = ObservationMarkPeer::doSelect(new Criteria());
      $this->setLayout('cleanLayout');
      $this->setTemplate('printStudentObservationsCard');
    }
}
