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
 * student_attendance actions.
 *
 * @package    sistema de alumnos
 * @subpackage student_attendance
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class student_attendanceActions extends sfActions
{

  private function getDaysOfMonth($month)
  {
    $days = array();
    $limit = cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
    $start_date = strtotime("1-". $month ."-". date('Y') ."");

    for ($i = 0; $i < $limit; $i++)
    {
      $day = strtotime("+$i day", $start_date);
      if (date('N', $day) < 6)
      {
        $days[] = $day;
      }
    }

    return $days;

  }

  public function executePrintAttendanceTemplate(sfWebRequest $request)
  {
    $this->month = $request->getParameter('month');
    $this->url = $request->getParameter('url');
    $this->id = $request->getParameter('id');
    $this->nextMonth = ($this->month < 12) ? $this->month + 1 : 1;
    $this->prevMonth = ($this->month > 1) ? $this->month - 1 : 12;
    $this->monthName = date("F", mktime(0, 0, 0, $this->month, 1, 2015));

    if ( $this->url == "division")
    {
      $this->division_id = $request->getParameter('division_id');
      $this->division = DivisionPeer::retrieveByPK($this->id);
      $this->students = $this->division->getStudents();
      $this->days = $this->getDaysOfMonth($this->month);
      $this->course_subject = null;

    }
    elseif ($this->url == "commission")
    {
      $this->course_subject = CourseSubjectPeer::retrieveByPK($this->id);
      $this->students = $this->course_subject->getStudents();
      $this->days = $this->getDaysOfMonth($this->month);

    }

    $this->setLayout('cleanLayout');
  }

  public function executeSelectValuesForAttendanceDay(sfWebRequest $request)
  {
    $this->getUser()->clearAttribute('back_url');
    $this->form = new SelectValuesForAttendanceDayForm();
    $this->url_action = 'student_attendance/SelectValuesForAttendanceDay';
    
    if ($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $request->setParameter('back_url', 'student_attendance/SelectValuesForAttendanceDay');
        $this->getUser()->setAttribute('back_url', 'student_attendance/SelectValuesForAttendanceDay');
        $this->forward('student_attendance', 'StudentAttendance');
      }
    }

  }

  public function executeSelectValuesForAttendanceSubject(sfWebRequest $request)
  {
    $this->getUser()->clearAttribute('back_url');
    $this->form = new SelectValuesForAttendanceSubjectForm();
    $this->url_action = 'student_attendance/SelectValuesForAttendanceSubject';
    
    if ($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {

        $multiple_student_attendance = $request->getParameter('multiple_student_attendance');
        $multiple_student_attendance['day'] = $this->form->getValue('day');
        $request->setParameter('multiple_student_attendance',$multiple_student_attendance);
        $request->setParameter('back_url', 'student_attendance/SelectValuesForAttendanceSubject');
        $this->getUser()->setAttribute('back_url', 'student_attendance/SelectValuesForAttendanceSubject');
        $this->forward('student_attendance', 'StudentAttendance');
      }
    }

  }

  public function executeFree(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('student_id', $request->getParameter('student_id'));
    $this->redirect('@student_free');
  }

  public function executeReincorporate(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('student_id', $request->getParameter('student_id'));

    $this->redirect('@student_reincorporation');
  }

  public function executeMultipleSubjectsCommissionAttendance(sfWebRequest $request) {
    $this->course = CoursePeer::retrieveByPk($request->getParameter('course'));
    $this->course_subjects = $this->course->getCourseSubjects();

  }

  public function executeStudentAttendance(sfWebRequest $request)
  {
    
    $params = $request->getParameter('multiple_student_attendance');
    unset($params['_csrf_token']);
    $course_subject = CourseSubjectPeer::retrieveByPK($request->getParameter('course_subject_id'));
    
    if (is_null($params))
    {
      $params['back_url'] = (!is_null($course_subject) && $course_subject->getCourse()->isPathway())?'pathway_commission':$request->getParameter('url');
      $params['year'] = $request->getParameter('year');
      $params['day'] = $request->getParameter('day') === null ? date('Y-m-d') : $request->getParameter('day');
      $params['division_id'] = $request->getParameter('division_id');
      $params['course_subject_id'] = $request->getParameter('course_subject_id') == '' ? null : $request->getParameter('course_subject_id');
      $params['career_school_year_id'] = $request->getParameter('career_school_year_id');
    }
    
    $multiple_student_attendance_form = (!is_null($course_subject) && $course_subject->getCourse()->getIsPathway()) ? SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendancePathwayForm(): SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendanceForm();
    
    $this->form = new $multiple_student_attendance_form;
    $this->form->setDefaults($params);
    $this->form->configureStudents();

    if (!isset($params['back_url']))
    {
      $this->back_url = ($request->getParameter('back_url'))? $request->getParameter('back_url'): $this->getUser()->getAttribute('back_url');
    }
    else
    {
      $this->back_url = $params['back_url'];
    }
    if($params['division_id'] == '')
    {
	$this->getUser()->setAttribute('back_url', 'student_attendance/StudentAttendance?url=division&year='.$params['year'].'&course_subject_id='.$params['course_subject_id'].'&career_school_year_id='.$params['career_school_year_id'].'&division_id=');
    }
    else
    {
    	$this->getUser()->setAttribute('back_url', 'student_attendance/StudentAttendance?url=division&year='.$params['year'].'&division_id='.$params['division_id'].'&career_school_year_id='.$params['career_school_year_id'].'&course_subject_id='); 
    }
	 
    $this->title = $this->form->isAttendanceBySubject() ? 'Load attendance for %subject%' : 'Load attendance day for %division%';
   
  }

  public function executeSaveStudentAttendance(sfWebRequest $request)
  {   
      
    $multiple_student_attendance = $request->getParameter('multiple_student_attendance');
    $course_subject = CourseSubjectPeer::retrieveByPK($multiple_student_attendance['course_subject_id']);
    $multiple_student_attendance_form = (!is_null($course_subject) && $course_subject->getCourse()->getIsPathway()) ? SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendancePathwayForm(): SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendanceForm();

    $this->form = new $multiple_student_attendance_form;
    
    $this->form->setDefault('year', $multiple_student_attendance['year']);
    $this->form->setDefault('day', $multiple_student_attendance['day']);
    $this->form->setDefault('career_school_year_id', $multiple_student_attendance['career_school_year_id']);
    $this->form->setDefault('course_subject_id', $multiple_student_attendance['course_subject_id']);
    $this->form->setDefault('division_id', $multiple_student_attendance['division_id']);

    $this->form->configureStudents();
    $this->title = $this->form->isAttendanceBySubject() ? 'Load attendance for %subject%' : 'Load attendance day for %division%';
    
    $this->back_url = (!is_null($course_subject) && $course_subject->getCourse()->getIsPathway())?'pathway_commission': $request->getParameter('back_url');
   

    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();
      $days_disabled =$this->form->configureStudents();
      $this->form->configureDaysWidget($days_disabled);
      $this->getUser()->setFlash('notice', 'The item was updated successfully.');
    }

    //formateo el dia yyyy-mm-dd para poderlo agregar a la url sin problemas //
    $day = str_replace('/', '-', $multiple_student_attendance['day']);
  
    if ($request->hasParameter("previous_division"))
    {
      $this->redirect("student_attendance/StudentAttendance?url=division&year=". $multiple_student_attendance['year'] . "&division_id=" . $this->form->getPreviousDivision()->getId() . "&career_school_year_id=" . $multiple_student_attendance['career_school_year_id']  . "&day=" .$day. "&course_subject_id=");
    }
    elseif ($request->hasParameter("next_division"))
    {
      $this->redirect("student_attendance/StudentAttendance?url=division&year=". $multiple_student_attendance['year'] . "&division_id=" . $this->form->getNextDivision()->getId() . "&career_school_year_id=" . $multiple_student_attendance['career_school_year_id'] . "&day=" .$day. "&course_subject_id=");
    }
    elseif ($request->hasParameter("print_attendance_template"))
    {
      $this->month = date('m');

      if (!$this->form->isAttendanceBySubject())
      {  
        $this->redirect("student_attendance/printAttendanceTemplate?url=division&id=" . $multiple_student_attendance['division_id']. " &month=". $this->month ."");
      }
      else 
      {
        $this->redirect("student_attendance/printAttendanceTemplate?url=commission&id=" . $multiple_student_attendance['course_subject_id']. " &month=". $this->month ."");
      }    
    }

    $this->setTemplate('StudentAttendance');
  }
  
  public function executeSelectValuesForAttendanceDayShowDay(sfWebRequest $request)
  {
	$this->getUser()->clearAttribute('back_url');
    $this->form = new SelectValuesForAttendanceDayForm();
    $this->url_action = 'student_attendance/SelectValuesForAttendanceDayShowDay';
    
    if ($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $request->setParameter('back_url', 'student_attendance/SelectValuesForAttendanceDayShowDay');
        $this->getUser()->setAttribute('back_url', 'student_attendance/SelectValuesForAttendanceDayShowDay');
        $this->forward('student_attendance', 'StudentAttendanceShowDay');
      }
    }
    
    $this->setTemplate('SelectValuesForAttendanceDay');  
  }
  
  public function executeStudentAttendanceShowDay(sfWebRequest $request)
  {
	$params = $request->getParameter('multiple_student_attendance');
	  
	if(is_null($params))
	{
	  $params['year'] = $request->getParameter('year');
	  $params['day'] = $request->getParameter('day') === null ? date('Y-m-d') : $request->getParameter('day');
	  $params['division_id'] = $request->getParameter('division_id');
	  $params['course_subject_id'] = $request->getParameter('course_subject_id') == '' ? null : $request->getParameter('course_subject_id');
	  $params['career_school_year_id'] = $request->getParameter('career_school_year_id');
		  
	}
	  
	$multiple_student_attendance_day_form = SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendanceDayForm();

	$this->form = new $multiple_student_attendance_day_form;
	$this->form->setDefaults($params);
	$this->form->configureStudents();

	$this->title = $this->form->isAttendanceBySubject() ? 'Load attendance for %subject%' : 'Load attendance day for %division%';
  }
  
  public function executeSaveStudentAttendanceShowDay(sfWebRequest $request)
  {
    $multiple_student_attendance_day_form = SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendanceDayForm();

    $this->form = new $multiple_student_attendance_day_form;
    $multiple_student_attendance = $request->getParameter('multiple_student_attendance');

    $this->form->setDefault('year', $multiple_student_attendance['year']);
    $this->form->setDefault('day', $multiple_student_attendance['day']);
    $this->form->setDefault('career_school_year_id', $multiple_student_attendance['career_school_year_id']);
    $this->form->setDefault('course_subject_id', $multiple_student_attendance['course_subject_id']);
    $this->form->setDefault('division_id', $multiple_student_attendance['division_id']);

    $this->form->configureStudents();
    $this->title = $this->form->isAttendanceBySubject() ? 'Load attendance for %subject%' : 'Load attendance day for %division%';
    $this->back_url = $request->getParameter('back_url');

    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();
      $this->getUser()->setFlash('notice', 'The item was updated successfully.');
    }
    
    $day = str_replace('/', '-', $multiple_student_attendance['day']);
  
    if ($request->hasParameter("previous_division"))
    {
      $this->redirect("student_attendance/StudentAttendanceShowDay?url=division&year=". $multiple_student_attendance['year'] . "&division_id=" . $this->form->getPreviousDivision()->getId() . "&career_school_year_id=" . $multiple_student_attendance['career_school_year_id']  . "&day=" .$day. "&course_subject_id=");
    }
    elseif ($request->hasParameter("next_division"))
    {
      $this->redirect("student_attendance/StudentAttendanceShowDay?url=division&year=". $multiple_student_attendance['year'] . "&division_id=" . $this->form->getNextDivision()->getId() . "&career_school_year_id=" . $multiple_student_attendance['career_school_year_id'] . "&day=" .$day. "&course_subject_id=");
    }

    $this->setTemplate('StudentAttendanceShowDay');
  
  }
  
  public function executeSelectValuesForAttendanceSubjectShowDay(sfWebRequest $request)
  {
    $this->getUser()->clearAttribute('back_url');
    $this->form = new SelectValuesForAttendanceSubjectForm();
    $this->url_action = 'student_attendance/SelectValuesForAttendanceSubjectShowDay';
    if ($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {

        $multiple_student_attendance = $request->getParameter('multiple_student_attendance');
        $multiple_student_attendance['day'] = $this->form->getValue('day');
        $request->setParameter('multiple_student_attendance',$multiple_student_attendance);
        $request->setParameter('back_url', 'student_attendance/SelectValuesForAttendanceSubjectShowDay');
        $this->getUser()->setAttribute('back_url', 'student_attendance/SelectValuesForAttendanceSubjectShowDay');
        $this->forward('student_attendance', 'StudentAttendanceShowDay');
      }
    }
    $this->setTemplate('SelectValuesForAttendanceSubject'); 
  }

}
