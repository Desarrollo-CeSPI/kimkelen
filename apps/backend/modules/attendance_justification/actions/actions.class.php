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
 * attendance_justification actions.
 *
 * @package    sistema de alumnos
 * @subpackage attendance_justification
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class attendance_justificationActions extends sfActions
{
  protected $filter_criteria = null;
  
  public function executeIndex(sfWebRequest $request)
  {
    if ($request->getParameter('ids'))
    {
      $this->forward('attendance_justification','justificate');
    }

    $filter_class = SchoolBehaviourFactory::getInstance()->getFormFactory()->getAttendanceJustificationFormFilter();
    $this->form   = new $filter_class($this->getFilterCriteria());

    $this->has_subject_attendance = SchoolBehaviourFactory::getInstance()->hasSubjectAttendance();

    if ($request->isMethod('POST'))
    {
      $params = $request->getParameter('attendance_justification');
      
      $this->form->bind($params);
      
      if ($this->form->isValid())
      {
        $this->setFilterCriteria($this->form->getValues());

        $criteria = $this->buildCriteria();

        $this->student_attendances = StudentAttendancePeer::doSelect($criteria);
      }
    }
    else
    {
      $criteria = $this->buildCriteria();

      $this->student_attendances = StudentAttendancePeer::doSelect($criteria);
    }
  }

  public function buildCriteria(Criteria $criteria = null)
  {
    if (null === $criteria)
    {
      $criteria = new Criteria();
    }

    $params = $this->getFilterCriteria();

    $criteria->setLimit(50);

    if (isset($params['attendance_subject']) && $params['attendance_subject'])
    {
      $criteria->add(StudentAttendancePeer::COURSE_SUBJECT_ID, null, Criteria::ISNOTNULL);
    }

    if (isset($params['from_date']) && trim($params['from_date']) != '')
    {
      $criteria->add(StudentAttendancePeer::DAY, $params['from_date'], Criteria::GREATER_EQUAL);
    }

    if (isset($params['to_date']) && trim($params['to_date']) != '')
    {
      $criteria->addAnd(StudentAttendancePeer::DAY, $params['to_date'], Criteria::LESS_EQUAL);
    }

    if (isset($params['student']) && trim($params['student']) != '')
    {
      $criteria->add(PersonPeer::LASTNAME, '%' . $params['student'] . '%', Criteria::LIKE);
      $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
      $criteria->addJoin(StudentPeer::ID, StudentAttendancePeer::STUDENT_ID);
    }

    if ($this->getUser()->isPreceptor())
    {
      PersonalPeer::joinWithStudents($criteria, $this->getUser()->getGuardUser()->getId());
      
      $criteria->addJoin(StudentPeer::ID, StudentAttendancePeer::STUDENT_ID);
    }

    $school_year = SchoolYearPeer::retrieveCurrent();

    //Filtro solo las que son faltas.
    $criteria->add(StudentAttendancePeer::VALUE, 0, Criteria::GREATER_THAN);

    $criteria->addJoin(StudentAttendancePeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());

    return $criteria;
  }

  public function executeJustificate(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

    $id = $request->getParameter('id');
    
    if ($id != '')
    {
      $this->student_attendance = StudentAttendancePeer::retrieveByPK($request->getParameter('id'));
      $this->student_attendances = array($this->student_attendance->getId());
      $this->student_attendance_justification = $this->student_attendance->getStudentAttendanceJustificationOrCreate();
    }
    else
    {

      $this->student_attendance_justification = new StudentAttendanceJustification();
      $this->student_attendances = $request->getParameter('ids');

    }

    if (StudentAttendancePeer::areAllFromSameStudent($this->student_attendances))
    {
      $this->form = new StudentAttendanceJustificationForm($this->student_attendance_justification);
      $this->form->setStudentAttendances($this->student_attendances);
    }
    else
    {
      $this->getUser()->setFlash('error', __('Cant do multiple justification with diferent students'));
      $this->forward('attendance_justification', 'index');
    }
  }

  public function executeSaveJustification(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

    $params = $request->getParameter('student_attendance_justification');
    $this->student_attendances = $request->getParameter('ids');
    $this->student_attendance_justification = StudentAttendanceJustificationPeer::retrieveByPK($params['id']);

    if(is_null($this->student_attendance_justification))
      $this->student_attendance_justification = new StudentAttendanceJustification();

    $this->form = new StudentAttendanceJustificationForm($this->student_attendance_justification);
    $this->form->setStudentAttendances($this->student_attendances);

    $this->form->bind($params, $request->getFiles('student_attendance_justification'));
    if ($this->form->isValid())
    {
      $this->form->save();
      $this->redirect('attendance_justification');
    }
    else
    {
      $this->getUser()->setFlash('error', __('The item has not been saved due to some errors.'));
      $this->setTemplate('justificate');
    }
  }

  public function executeDelete(sfWebRequest $request)
  {
    $student_attendance_justification = StudentAttendanceJustificationPeer::retrieveByPK($request->getParameter('id'));
    $student_attendance_justification->delete();

    $this->getUser()->setFlash('notice', 'The justification has been deleted succesfuly.');
    $this->forward('attendance_justification', 'index');
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->student_attendance_justification = StudentAttendanceJustificationPeer::retrieveByPK($request->getParameter('id'));
  }

  public function setFilterCriteria(array $criteria)
  {
    $this->filter_criteria = $criteria;

    $this->getUser()->setAttribute('attendance_justification.filter_criteria', $criteria);
  }

  public function getFilterCriteria()
  {
    if (null === $this->filter_criteria)
    {
      $this->filter_criteria = $this->getUser()->getAttribute('attendance_justification.filter_criteria', array());
    }

    return $this->filter_criteria;
  }

}