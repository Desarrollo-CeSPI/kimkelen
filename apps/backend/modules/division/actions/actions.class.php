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

require_once dirname(__FILE__) . '/../lib/divisionGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/divisionGeneratorHelper.class.php';

/**
 * division actions.
 *
 * @package    sistema de alumnos
 * @subpackage division
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class divisionActions extends autoDivisionActions
{

  public function executeMoveStudents(sfWebRequest $request)
  {
    $this->origin_division = DivisionPeer::retrieveByPK($request->getParameter('id'));
    $this->form = new MoveStudentsToDivisionForm(array(), array('division' => $this->origin_division));
    $this->back_url = 'division/divisionStudents?id='. $this->origin_division->getId();
  }

  public function executeUpdateMoveStudents(sfWebRequest $request)
  {
    $this->origin_division = DivisionPeer::retrieveByPK($request->getParameter('id'));
    $this->form = new MoveStudentsToDivisionForm(array(), array('division' => $this->origin_division));
    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    $this->back_url = 'division/divisionStudents?id='. $this->origin_division->getId();

    if ($this->form->isValid())
    {
      try
      {
        $parameters = $request->getParameter('move_students');
        $destiny_division = DivisionPeer::retrieveByPK($parameters['destiny_division_id']);
        $students = $parameters['students'];

        $destiny_division->addStudentsFromDivision($students, $this->origin_division);
        $this->getUser()->setFlash('notice', 'Los alumnos seleccionados han sido correctamente movidos de división.');
      }
      catch (Exception $e)
      {
        $this->getUser()->setFlash('error', 'Ocurrieron errores que no permitieron concretar la acción. Comprueba que tanto la división origen como la destino no posean cursos ya cerrados.');
      }
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    $this->setTemplate('moveStudents');
  }

  public function executeDivisionPreceptors(sfWebRequest $request)
  {
    $this->division = $this->getRoute()->getObject();
    $this->form = new DivisionPreceptorManyForm($this->division);
  }

  public function executeBatchDivisionPreceptors(sfWebRequest $request, $objects)
  {
    $ids = $request->getParameter('ids');
    // HACER!!!
    $this->redirect('division');
  }

  public function executeUpdateDivisionPreceptors(sfWebRequest $request)
  {
    $this->division = DivisionPeer::retrieveByPk($request->getParameter('division[id]'));

    if (null === $this->division)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una division para configurar sus preceptores');

      $this->redirect('@division');
    }

    $this->form = new DivisionPreceptorManyForm($this->division);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los preceptores seleccionados han sido correctamente asignado a la división.');
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    $this->setTemplate('divisionPreceptors');

  }

  public function executeDivisionStudents()
  {
    $this->division = $this->getRoute()->getObject();
    $this->form = new DivisionStudentInscriptionForm($this->division);
  }

  public function executeUpdateDivisionStudents(sfWebRequest $request)
  {
    $this->division = DivisionPeer::retrieveByPk($request->getParameter('division[id]'));

    if (null === $this->division)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una division para inscribir a los estudiantes');

      $this->redirect('@division');
    }

    $this->form = new DivisionStudentInscriptionForm($this->division);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      try{
      $this->form->save();
      $this->getUser()->setFlash('notice', 'Los alumnos seleccionados han sido correctamente inscriptos a la división.');
      }
      catch (Exception $e){
        $this->getUser()->setFlash('error', $e->getMessage());
      }
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    $this->setTemplate('divisionStudents');
  }

  public function executeDivisionCourses(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);
    $this->redirect('@division_course');
  }

  public function executeShowCalendar(sfWebRequest $request)
  {
    $this->division = $this->getRoute()->getObject();
    $this->events = json_encode($this->division->getWeekCalendar());
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));

    if ($form->isValid())
    {
      $division_is_new = $form->getObject()->isNew();
      $notice = $this->getProcessFormNotice($division_is_new);

      $division = $form->save();

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $division)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->setProcessFormSaveAndAddFlash($notice);

        $this->redirect('@division_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        if ($request->hasParameter('_save_and_list'))
        {
          $this->redirect('@division');
        }
        else
        {
          $this->redirect('@division_edit?id=' . $division->getId());
        }
      }
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }

  }

  public function executeStudents(sfWebRequest $request)
  {
    $reference_array = array(
      "peer" => "DivisionStudentPeer",
      "fk" => "STUDENT_ID",
      "object_ids" => $this->getRoute()->getObject()->getStudentsIds(),
      "back_to" => "@division",
      "title" => "Division student list",
      "back_to_label" => "Return to division list"
    );

    $this->getUser()->setReferenceFor($this, $reference_array, "shared_student");

    $this->redirect('@shared_student');

  }

  public function executePrintReportCard(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);

    $this->redirect("@report_card");

  }

  public function executeStudentConduct(sfWebRequest $request)
  {
    $this->division = $this->getRoute()->getObject();
    $this->students = $this->division->getStudents();
    #$students = $this->division->getStudents();
    $career_school_year = $this->division->getCareerSchoolYear();
    $this->periods = $this->division->getCareerSchoolYearPeriods();

    $form = new StudentsCareerSchoolYearConductForm();
    $form->setStudents($this->students, $career_school_year);
    $this->form = $form;

  }

  public function executeUpdateConduct(sfWebRequest $request)
  {
    $this->division = DivisionPeer::retrieveByPk($request->getParameter('division_id'));
    $this->students = $this->division->getStudents();
    $this->periods = $this->division->getCareerSchoolYearPeriods();

    #   $students = $this->division->getStudents();
    $career_school_year = $this->division->getCareerSchoolYear();
    $this->form = new StudentsCareerSchoolYearConductForm();
    $this->form->setStudents($this->students, $career_school_year);
    //$form->setStudents($this->division->getStudents() ,$this->division->getCareerSchoolYear());

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los alumnos seleccionados han sido correctamente inscriptos a la división.');
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    $this->setTemplate('studentConduct');

  }

  public function executePrintCalification(sfWebRequest $request)
  {
    $this->setLayout('cleanLayout');

    $this->division = DivisionPeer::retrieveByPk($request->getParameter('id'));

    if (null === $this->division)
    {
      $this->getUser()->setFlash('error', 'No se ha indicado ninguna división');
      $this->redirect('@division');
    }

    $this->buildCalificationReport();
  }

  public function buildCalificationReport()
  {
    $this->students = $this->division->getStudents();
    $this->courses = $this->division->getCourses();
    $this->career_subjects = $this->division->getCareerSubjects();

    $this->career_subject_school_years = array();
    $this->course_subjects = array();
    $this->configurations = array();

    foreach ($this->division->getCourseSubjects() as $i => $course_subject)
    {
      $this->career_subject_school_years[$i] = $cssy = $course_subject->getCareerSubjectSchoolYear();

      $this->course_subjects[$i] = $course_subject;

      $this->configurations[$i] = $course_subject->getCareerSubjectSchoolYear()->getConfiguration();
    }
  }

  public function executeExportCalificationTable(sfWebRequest $request)
  {
    $this->division = DivisionPeer::retrieveByPk($request->getParameter('id'));
    $this->buildCalificationReport();

    sfConfig::set('sf_web_debug', false);

    $this->setLayout(false);

    $renderer = new DivisionCalificationReportRenderer();

    ini_set("max_execution_time",0);

    //Title & headers
    $renderer->renderTitle(sprintf("Planilla de calificaciones - Fecha: %s", date("d/m/Y")));
    $renderer->renderSubTitle(sprintf("Division %s, año lectivo %s", $this->division, $this->division->getSchoolYear()));

    $renderer->renderColumnHeaders($this->course_subjects);

    //Building column headers
    $renderer->renderCourseSubjectHeader($this->configurations);
    //Building body rows
    foreach ($this->students as $student)
    {
      $renderer->renderStudentCalificationRow($student, $this->course_subjects);
    }

    foreach($renderer->getHtmlHeaders() as $field => $name)
    {
      $this->getResponse()->setHttpHeader($field,$name);
    }

    return $this->renderText($renderer->renderContent());
  }
  /*
  public function executeExportCalificationTable(sfWebRequest $request)
  {
    $this->executePrintCalification($request);

    $response = $this->getResponse();

    $response->setHttpHeader("Content-type","application/vnd.ms-excel; name='excel'; charset='utf-8'");
    $response->setHttpHeader('Content-Disposition', 'attachment; filename="planilla_calificaciones.xls"');
    $response->setHttpHeader("Pragma","no-cache");
    $response->setHttpHeader("Expires","0");
  }
  */

  public function executeCourseConfiguration(sfWebRequest $request)
  {
    $this->division = $this->getRoute()->getObject();
    $this->form = $this->division->getConfigurationForm($this->division->getCourseType());
    $this->referer_module = "division";
  }

  public function executeUpdateCourseConfiguration(sfWebRequest $request, $con = null)
  {
    $this->referer_module = "division";
    if (!$request->isMethod("post"))
    {
      $this->redirect('division/index');
    }
    $this->division = DivisionPeer::retrieveByPK($request->getParameter('id'));
    $course_type = $this->division->getCourseType();
    $this->form = $this->division->getConfigurationForm($course_type);
    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save($con);
      $this->getUser()->setFlash('notice', 'The item was updated successfully.');
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.');
    }
    $this->setTemplate('courseConfiguration');

  }

  public function executeAttendanceSheetByDay(sfWebRequest $request)
  {
    $this->division = DivisionPeer::retrieveByPK($request->getParameter('id'));
    $this->form = new AttendanceSheetForm();
    $this->form->setDefault('division_or_course_id', $this->division->getId());
    $this->url = '@divisionAttendanceDay';
    $this->module = '@division';
    $this->setTemplate('chooseAttendanceSheetDateRange');

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
      $this->user_course_subject = false;
      $attendance_sheet = $request->getParameter("attendance_sheet");
      $this->division = DivisionPeer::retrieveByPK($attendance_sheet['division_or_course_id']);
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
        $this->students = $this->division->getStudents();
        $this->course_subject = null;
        $this->course_subject_id = null;

        $this->setLayout('cleanLayout');
      }
      else
      {
        $this->getUser()->setFlash('error', 'Hay errores en los datos ingresados. No deje campos en blanco e ingrese fechas válidas dentro de los períodos escolares.');
        $this->redirect('division/attendanceSheetByDay?id=' . $this->division->getId());
      }
    }

  }

  public function executeAttendanceDay(sfWebRequest $request)
  {
    $division = $this->getRoute()->getObject();
    $career_school_year_id = $division->getCareerSchoolYearId();
    $division_id = $division->getId();
    $year = $division->getYear();
    $this->redirect("student_attendance/StudentAttendance?url=division&year=$year&division_id=$division_id&career_school_year_id=$career_school_year_id&course_subject_id=");

  }

  public function executeShowStudents(sfWebRequest $request)
  {
    $division = DivisionPeer::retrieveByPK($request->getParameter('id'));

    return $this->renderPartial('division_students', array('division' => $division));

  }

  public function executeShowCourses(sfWebRequest $request)
  {
    $division = DivisionPeer::retrieveByPK($request->getParameter('id'));

    return $this->renderPartial('division_courses', array('division' => $division));

  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->redirect('@division');
  }

  public function executeConfigure(sfWebRequest $request)
  {
    $this->division = $this->getRoute()->getObject();
    $this->form = new CourseSubjectConfigurationDivisionForm();
  }
}