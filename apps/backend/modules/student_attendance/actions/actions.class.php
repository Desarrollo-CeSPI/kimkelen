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

  public function executeSelectValuesForAttendanceDay(sfWebRequest $request)
  {
    $this->getUser()->clearAttribute('back_url');
    $this->form = new SelectValuesForAttendanceDayForm();
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

  public function executeStudentAttendance(sfWebRequest $request)
  {
    $params = $request->getParameter('multiple_student_attendance');
    unset($params['_csrf_token']);

    if (is_null($params))
    {
      $params['back_url'] = $request->getParameter('url');
      $params['year'] = $request->getParameter('year');
      $params['day'] = $request->getParameter('day') === null ? date('Y-m-d') : $request->getParameter('day');
      $params['division_id'] = $request->getParameter('division_id');
      $params['course_subject_id'] = $request->getParameter('course_subject_id') == '' ? null : $request->getParameter('course_subject_id');
      $params['career_school_year_id'] = $request->getParameter('career_school_year_id');
    }


    $multiple_student_attendance_form = SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendanceForm();

    $this->form = new $multiple_student_attendance_form;
    $this->form->setDefaults($params);
    $this->form->configureStudents();

    if (!isset($params['back_url']))
    {
      $this->back_url = ($request->getParameter('back_url'))? $request->getParameter('back_url'): $this->getUser()->getAttribute('back_url'); ;
    }
    else
    {
      $this->back_url = $params['back_url'];
    }

    $this->title = $this->form->isAttendanceBySubject() ? 'Load attendance for %subject%' : 'Load attendance day for %division%';

  }

  public function executeSaveStudentAttendance(sfWebRequest $request)
  {
    $multiple_student_attendance_form = SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleStudentAttendanceForm();

    $this->form = new $multiple_student_attendance_form;
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
      $days_disabled =$this->form->configureStudents();
      $this->form->configureDaysWidget($days_disabled);
      $this->getUser()->setFlash('notice', 'The item was updated successfully.');
    }

    if ($request->hasParameter("previous_division"))
    {
      $this->redirect("student_attendance/StudentAttendance?url=division&year=". $multiple_student_attendance['year'] . "&division_id=" . $this->form->getPreviousDivision()->getId() . "&career_school_year_id=" . $multiple_student_attendance['career_school_year_id'] . "&course_subject_id=");
    }
    elseif ($request->hasParameter("next_division"))
    {
      $this->redirect("student_attendance/StudentAttendance?url=division&year=". $multiple_student_attendance['year'] . "&division_id=" . $this->form->getNextDivision()->getId() . "&career_school_year_id=" . $multiple_student_attendance['career_school_year_id'] . "&course_subject_id=");
    }

    $this->setTemplate('StudentAttendance');
  }

}