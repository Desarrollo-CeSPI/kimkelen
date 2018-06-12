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
 * rating_report actions.
 *
 * @package    sistema de alumnos
 * @subpackage rating_report
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class rating_reportActions extends sfActions
{

  public function executeFilterForSubject(sfWebRequest $request)
  {
    $this->form = new SubjectRatingReportFormFilter();
    if ($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->getUser()->setReferenceFor($this, 'rating_report');
        $this->forward('rating_report', 'subject');
      }
    }

    $this->route = "rating_report/filterForSubject";
  }

  public function executeSubject(sfWebRequest $request)
  {
  	$params = $request->getParameter('subject_rating_report');
        $this->getUser()->setReferenceFor($this, 'rating_report');
        $this->redirect('course_student_mark/printSubjectCalification?id=' . $params['course_subject_id'] );
  }

  public function executeFilterForDivision(sfWebRequest $request)
  {

    $this->form = new DivisionRatingReportFormFilter();
    if ($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $params = $request->getParameter('division_rating_report');        

        $this->getUser()->setReferenceFor($this, 'rating_report');
        $this->redirect('division/printCalification?id=' . $params['division_id']);
      }
    }

    $this->route = "rating_report/filterForDivision";
  }
  
    public function executeFilterBySchoolYear(sfWebRequest $request)
    {
        $this->form = new SchoolYearAverageReportFormFilter();
      
        if ($request->isMethod('POST'))
        {
          $this->form->bind($request->getParameter($this->form->getName()));
          if ($this->form->isValid())
          {
             $this->year = $request->getParameter('average_report[year]');
             
             $this->career_school_year = $request->getParameter('average_report[career_school_year_id]');
             $status = array(StudentCareerSchoolYearStatus::WITHDRAWN,StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE);       
             
             $c = new Criteria();
             $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID,$this->career_school_year);
             $c->add(StudentCareerSchoolYearPeer::YEAR,$this->year);
             $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID,StudentPeer::ID);
             $c->add(StudentCareerSchoolYearPeer::STATUS, $status , Criteria::NOT_IN);
             $c->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
             $c->addAscendingOrderByColumn(PersonPeer::LASTNAME);
             
             $this->students = StudentPeer::doSelect($c);
             $this->setLayout('cleanLayout');
             $this->setTemplate('printAverage');
            
          }
         
        }

        $this->route = "rating_report/filterBySchoolYear";
    }
}