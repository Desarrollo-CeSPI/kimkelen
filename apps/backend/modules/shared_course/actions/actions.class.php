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

require_once dirname(__FILE__) . '/../lib/shared_courseGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/shared_courseGeneratorHelper.class.php';

/**
 * shared_course actions.
 *
 * @package    sistema de alumnos
 * @subpackage shared_course
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class shared_courseActions extends autoShared_courseActions
{
  public function preExecute()
  {
    parent::preExecute();

    $this->referer_module = $this->getUser()->getAttribute("referer_module");
  }

  /**
   * Días de cursada
   */
  public function executeManageCourseDays(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->course_subjects = $this->course->getCourseSubjects();
    $this->handleSelectedTab($request);
    $this->forms = $this->getCourseSubjectForms($this->course_subjects);

  }

  public function handleSelectedTab(sfWebRequest $request)
  {
    if ($request->hasParameter("selected"))
    {
      $this->selected = $request->getParameter("selected");
    }
    else
    {
      // siempre hay uno.
      $this->selected = $this->course_subjects[0]->getId();
    }
  }

  public function getCourseSubjectForms()
  {
    $forms = array();

    $course_subject = CourseSubjectPeer::retrieveByPK($this->selected);

    $forms[$course_subject->getId()] = new ManageCourseSubjectDayForm($course_subject);

    return $forms;
  }

  public function executeUpdateCourseDays(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPK($request->getParameter("id"));
    if (!$request->isMethod('POST') || is_null($this->course))
    {
      $this->redirect('division_course/index');
    }


    $this->course_subjects = $this->course->getCourseSubjects();
    $this->handleSelectedTab($request);
    $this->forms = $this->getCourseSubjectForms($this->course_subjects);

    $valid = count($this->forms);

    foreach ($this->forms as $form)
    {
      $form->bind($request->getParameter($form->getName()));

      if ($form->isValid())
      {
        $valid--;
      }
    }

    if ($valid == 0)
    {
      foreach ($this->forms as $form)
      {
        $form->save();
      }

      $this->getUser()->setFlash('notice', 'Hours of courses have been saved correctly.');
      $this->redirect('shared_course/manageCourseDays?id=' . $this->course->getId());
    }
    else
    {
      $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar guardar los horarios de cursada. Por favor, intente nuevamente la operación.');
    }

    $this->setTemplate('manageCourseDays');
  }

  /**
   * alumnos
   */
  public function executeCourseSubjectStudent(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->course_subjects = $this->course->getCourseSubjects();
    $this->forms = $this->getForms($this->course_subjects);

    $this->handleSelectedTab($request);
  }

  public function getForms($course_subjects)
  {
    $forms = array();
    $i = 0;
    foreach ($course_subjects as $course_subject)
    {
      $forms[$course_subject->getId()] = new CourseSubjectStudentManyForm($course_subject);
      $forms[$course_subject->getId()]->getWidgetSchema()->setNameFormat("course_subject_${i}[%s]");
      $i++;
    }

    return $forms;
  }

  public function executeUpdateCourseSubjectStudents(sfWebRequest $request, $con = null)
  {
    if (!$request->isMethod("post"))
    {
      $this->redirect('division_course/index');
    }

    $this->course = CoursePeer::retrieveByPK($request->getParameter('id'));
    $this->course_subjects = $this->course->getCourseSubjects();
    $this->forms = $this->getForms($this->course_subjects);

    $this->handleSelectedTab($request);

    $valid = count($this->forms);

    foreach ($this->forms as $form)
    {
      $form->bind($request->getParameter($form->getName()));

      if ($form->isValid())
      {
        $valid--;
      }
    }

    if (is_null($con))
    {
      $con = Propel::getConnection(DivisionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    $con->beginTransaction();
    try
    {
      if ($valid == 0)
      {
        foreach ($this->forms as $form)
        {
          $form->save($con);
        }
        $this->getUser()->setFlash('notice', 'Los alumnos se guardaron satisfactoriamente.');
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar guardar los alumnos. Por favor, intente nuevamente la operación.');
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
      $this->getUser()->setFlash('error', $e->getMessage());
    }

    $this->setTemplate('courseSubjectStudent');
  }

  /**
   * close
   */
  public function executeClose(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
  }

  public function executeSaveClose(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    $this->course = CoursePeer::retrieveByPk($request->getParameter('id'));
    $this->course->close();
    $this->getUser()->setFlash('notice', __('The course has been closed successfuly'));
    $this->setTemplate('close');
  }

  /**
   * teachers
   */
  public function executeCourseTeachers(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->course_subjects = $this->course->getCourseSubjects();
    $this->forms = $this->getTeacherForms($this->course_subjects);

    $this->handleSelectedTab($request);
  }

  public function getTeacherForms($course_subjects)
  {
    $forms = array();
    $i = 0;
    foreach ($course_subjects as $course_subject)
    {
      $forms[$course_subject->getId()] = new CourseSubjectTeacherForm($course_subject);
      $forms[$course_subject->getId()]->getWidgetSchema()->setNameFormat("course_subject_${i}[%s]");
      $i++;
    }
    return $forms;
  }

  public function executeUpdateCourseTeachers(sfWebRequest $request)
  {
    if (!$request->isMethod("post"))
    {
      $this->redirect('division_course/index');
    }

    $this->course = CoursePeer::retrieveByPK($request->getParameter('id'));
    $this->course_subjects = $this->course->getCourseSubjects();
    $this->forms = $this->getTeacherForms($this->course_subjects);

    $this->handleSelectedTab($request);

    $valid = count($this->forms);

    foreach ($this->forms as $form)
    {
      $form->bind($request->getParameter($form->getName()));

      if ($form->isValid())
      {
        $valid--;
      }
    }

    if ($valid == 0)
    {
      foreach ($this->forms as $form)
      {
        $form->save();
      }

      $this->getUser()->setFlash('notice', 'Los profesores fueron agregados exitosamente.');
    }
    else
    {
      $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar guardar los profesores. Por favor, intente nuevamente la operación.');
    }

    $this->setTemplate('courseTeachers');
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->redirect('@homepage');
  }

  public function executeCourseConfiguration(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->course_subjects = $this->course->getCourseSubjects();

    $this->forms = $this->getCourseSubjectConfigurationForms($this->course_subjects);

    $is_bimester_attendance_subject = true;

    foreach ($this->course_subjects as $course_subject)
    { /* @var $course_subject CourseSubject */

      $is_bimester_attendance_subject = $is_bimester_attendance_subject && ($course_subject->getCourseType() == CourseType::BIMESTER) && count($course_subject->getCourseSubjectConfigurations()) > 0 && $course_subject->hasAttendanceForSubject();
    }

    $this->getUser()->setAttribute('course_subjects', $this->course_subjects);
    $this->is_bimester = $is_bimester_attendance_subject;

  }

  public function getCourseSubjectConfigurationForms($course_subjects)
  {
    $forms = array();
    $i = 0;
    $c = new Criteria();

    foreach ($course_subjects as $course_subject)
    {
      $course_type = $course_subject->getCareerSubjectSchoolYear()->getConfiguration()->getCourseType();

      $first_form = ($course_type == CourseType::BIMESTER || $course_type == CourseType::QUATERLY_OF_A_TERM || CourseType::BIMESTER_OF_A_TERM);
      $c->add(CourseSubjectConfigurationPeer::COURSE_SUBJECT_ID, $course_subject->getId());

      $new = (CourseSubjectConfigurationPeer::doSelect($c));

      #var_dump($is_bimester,!count($new), 'OR',$course_subject->hasAttendanceForDay());
      if ($first_form && !count($new) || ($first_form && $course_subject->hasAttendanceForDay()))
      {
        $forms[$course_subject->getId()] = new CourseSubjectConfigurationFirstForm($course_subject);
        $forms[$course_subject->getId()]->setCourseType($course_type);
      }
      else
      {
        $forms[$course_subject->getId()] = new CourseSubjectConfigurationManyForm($course_subject);
      }

      $forms[$course_subject->getId()]->getWidgetSchema()->setNameFormat("course_subject_${i}[%s]");
      $i++;
    }
    return $forms;
  }

  public function executeUpdateCourseConfiguration(sfWebRequest $request, $con = null)
  {
    if (!$request->isMethod("post"))
    {
      $this->redirect('commission/index');
    }
    $this->course = CoursePeer::retrieveByPK($request->getParameter('id'));
    $this->course_subjects = $this->course->getCourseSubjects();
    $this->forms = $this->getCourseSubjectConfigurationForms($this->course_subjects);

    foreach ($this->forms as $form)
    {
      $form->bind($request->getParameter($form->getName()));
      if ($form->isValid())
      {
        $form->save($con);
        $this->getUser()->setFlash('notice', 'The item was updated successfully.');
      }
      else
      {
        $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.');
      }
    }
    if ($this->isValidForm($this->forms, $request))
    {
      $this->forms = $this->getCourseSubjectConfigurationForms($this->course_subjects);
    }


    $is_bimester_attendance_subject = true;

    foreach ($this->course_subjects as $course_subject)
    { /* @var $course_subject CourseSubject */
      $is_bimester_attendance_subject = $is_bimester_attendance_subject && ($course_subject->getCourseType() == CourseType::BIMESTER) && $course_subject->hasAttendanceForSubject();
    }

    $this->is_bimester = ($is_bimester_attendance_subject && count($course_subject->getCourseSubjectConfigurations()) > 0);

    $this->getUser()->setAttribute('course_subjects', $this->course_subjects);

    $this->setTemplate('courseConfiguration');
  }

  public function isValidForm($forms,$request)
  {
    foreach ($forms as $form)
    {
      $form->bind($request->getParameter($form->getName()));
      if (!$form->isValid())
      {
        return false;
      }
    }
    return true;
  }

  public function executeDeleteCourseSubjectConfiguration(sfWebRequest $request, $con = null)
  {
    foreach ($this->getUser()->getAttribute('course_subjects') as $course_subject)
    {
      $course_subject->deleteCourseSubjectConfiguration();
    }
     $this->getUser()->setFlash("notice", "La configuración previa fue eliminada exitosamente", true);
     $this->redirect('@commission');
  }


  public function executeAttendanceSheetByCourseSubject(sfWebRequest $request)
  {
    $this->form = new AttendanceSheetForm();
    $this->course = $this->getRoute()->getObject();

    $this->form->setDefault('division_or_course_id', $this->course->getId());
    $this->url = '@courseAttendanceCourseSubject';
    $this->module = "@" . $this->getUser()->getAttribute('referer_module');
    $this->setTemplate('chooseAttendanceSheetDateRange', 'division');
  }

  private function getDays($start_date, $limit)
  {
    $days = array();

    for ($i = 0; $i <= $limit; $i++)
    {
      $days[] = strtotime("+$i day", $start_date);
    }

    return $days;

  }

  public function executeAttendanceSheet(sfWebRequest $request)
  {
    if ($request->isMethod('POST'))
    {
      $this->user_course_subject = true;
      $attendance_sheet = $request->getParameter("attendance_sheet");
      $this->course_id = $attendance_sheet['division_or_course_id'];
      $this->form = new AttendanceSheetForm();
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $date_range = $attendance_sheet['date_range'];
        $this->from_date = $date_range['from'];
        $this->to_date = $date_range['to'];
        list($day, $month, $year) = explode("/", $this->from_date);
        $start_date = mktime(date('H'), date('i'), date('s'), $month, $day, $year);
        list($day, $month, $year) = explode("/", $this->to_date);
        $end_date = mktime(date('H'), date('i'), date('s'), $month, $day, $year);

        $total_days = ($end_date - $start_date) / (60 * 60 * 24);

        $this->days = $this->getDays($start_date, $total_days);
        $this->course_subject = CourseSubjectPeer::retrieveByCourseId($this->course_id);
        $this->course_subject_id = $this->course_subject->getId();
        $this->students = $this->course_subject->getStudents();

		$this->setTemplate('attendanceSheet', 'division');
        $this->setLayout('cleanLayout');
      }
      else
      {
        $this->getUser()->setFlash('error', 'Por favor, complete los campos de fecha correctamente.');
        $this->redirect('commission/attendanceSheetByCourseSubject?id=' . $this->course_id);
      }
    }

  }

  public function executeMoveStudents(sfWebRequest $request)
  {
    $this->origin_course_subject = CourseSubjectPeer::retrieveByPK($request->getParameter('id'));
    $this->form = new MoveStudentsToCourseSubjectForm(array(), array('course_subject' => $this->origin_course_subject));
  }

  public function executeUpdateMoveStudents(sfWebRequest $request)
  {
    $this->origin_course_subject = CourseSubjectPeer::retrieveByPK($request->getParameter('id'));
    $this->form = new MoveStudentsToCourseSubjectForm(array(), array('course_subject' => $this->origin_course_subject));
    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      try
      {
        $parameters = $request->getParameter('move_students');
        $destiny_course_subject = CourseSubjectPeer::retrieveByPK($parameters['destiny_course_subject_id']);
        $students = $parameters['students'];
        $destiny_course_subject->addStudentsFromCourseSubject($students, $this->origin_course_subject);
        $this->getUser()->setFlash('notice', 'Los alumnos seleccionados han sido correctamente movidos de comisión.');
      }
      catch (Exception $e)
      {
        $this->getUser()->setFlash('error', 'Ocurrieron errores que no permitieron concretar la acción. Compruebe que las comisiones origen y destino no estén cerradas. Tampoco se permitirá mover alumnos si ya se calificó a alguno o si se pasó asistencia al curso origen.');
      }
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    $this->setTemplate('moveStudents');
  }

}
