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

require_once dirname(__FILE__).'/../lib/courseGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/courseGeneratorHelper.class.php';

/**
 * course actions.
 *
 * @package    sistema de alumnos
 * @subpackage course
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class courseActions extends autoCourseActions
{

  public function executeShow(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->form = $this->configuration->getForm($this->course);
    $this->getUser()->setAttribute("request_referer", $request->getReferer());
  }

  public function executeBack(sfWebRequest $request)
  {
    $this->redirect($this->getUser()->getAttribute("request_referer"));
  }

  public function executeManageCourseDays(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "course");

    $this->forward("shared_course", "manageCourseDays");
  }

  public function executeCourseSubjectStudent(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "course");

    $this->forward("shared_course", "courseSubjectStudent");
  }

  public function executeCalifications(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();

    $this->getUser()->setAttribute("referer_module", "course");

    $this->redirect("course_student_mark/index?id=".$this->course->getId());
  }

  public function executeCourseTeachers(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->form = new CourseTeachersForm($this->course);
  }

  public function executeUpdateTeachers (sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPk($request->getParameter('id'));

    if (null === $this->course)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar un curso para configurar sus profesores');

      $this->redirect('@course');
    }

    $this->form = new CourseTeachersForm($this->course);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {

      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los profesores seleccionados fueron exitosamente asignados al curso.');
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    $this->setTemplate('courseTeachers');
  }

  public function executePreceptors(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->form = new CoursePreceptorsForm($this->course);
  }

  public function executeUpdatePreceptors (sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPk($request->getParameter('id'));

    if (null === $this->course)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar un curso para configurar sus preceptores');

      $this->redirect('@course');
    }

    $this->form = new CoursePreceptorsForm($this->course);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los preceptores seleccionados han sido correctamente asignados al curso.');
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }

    $this->setTemplate('preceptors');
  }

  public function executeStudents(sfWebRequest $request)
  {
    $reference_array = array(
      "peer" => "CourseSubjectStudentPeer",
      "fk" => "COURSE_SUBJECT_ID",
      "object_ids" => $this->getRoute()->getObject()->getCourseSubjectIds(),
      "back_to" => "@course",
      "title" => "Course student list",
      "back_to_label" => "Return to course list",
    );

    $this->getUser()->setReferenceFor($this, $reference_array, "shared_student");

    $this->redirect('@shared_student');
  }

  public function executeCourseSubjectStudentsRegularity(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPK($request->getParameter('id'));
    if(empty($this->course))
      $this->redirect('@division_course');
    $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCourseSubjectStudentsRegularityForm();
    $this->form = new $form_name;
    $this->form->setCourse($this->course);
    if ($request->isMethod("POST"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash("notice", "All students states have been saved successfully.");
      }
    }

  }
}