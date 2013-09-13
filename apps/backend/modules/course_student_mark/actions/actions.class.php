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
 * course_student_mark actions.
 *
 * @package    sistema de alumnos
 * @subpackage course_student_mark
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 1599 2010-11-04 13:38:38Z gramirez $
 */
class course_student_markActions extends sfActions
{

  public function preExecute()
  {
    parent::preExecute();

    $this->referer_module = $this->getUser()->getAttribute("referer_module");

  }

  /**
   * Get Course object from user's context.
   *
   * @return Course
   */
  public function getCourse()
  {
    $course = CoursePeer::retrieveByPK($this->getRequest()->getParameter("id"));

    if (null === $course)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar un curso para editar sus calificaciones.');

      $this->redirect('@course');
    }
    else if (!$course->canEditMarks())
    {
      $this->getUser()->setFlash('error', 'El curso seleccionado no permite la edición de notas: o bien no tiene alumnos o bien el año lectivo al que pertenece no permite la edición de calificaciones.');

      $this->redirect('@course');
    }

    return $course;

  }

  protected function getForms($course_subjects)
  {
    $forms = array();

    foreach ($course_subjects as $course_subject)
    {
      $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCourseSubjectMarksForm();
      $forms[$course_subject->getId()] = new $form_name($course_subject);
    }

    return $forms;

  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->course = $this->getCourse();
    $this->course_subjects = $this->course->getCourseSubjectsForUser($this->getUser());
    $this->forms = $this->getForms($this->course_subjects);

  }

  public function executePrint(sfWebRequest $request)
  {

    $this->setLayout('cleanLayout');

    $this->course = CoursePeer::retrieveByPK($this->getRequest()->getParameter("id"));

    $this->course_subjects = $this->course->getCourseSubjectsForUser($this->getUser());

    #$this->forms           = $this->getForms($this->course_subjects);

  }

  public function executePrintTable(SfWebRequest $request)
  {
    $this->setLayout('cleanLayout');

    $this->table = $request->getParameter("send_data");

    $response = $this->getResponse();

    $response->setHttpHeader("Content-type", "application/vnd.ms-excel; name='excel'; charset='utf-8'");
    $response->setHttpHeader('Content-Disposition', 'attachment; filename="planilla_calificaciones.xls"');
    $response->setHttpHeader("Pragma", "no-cache");
    $response->setHttpHeader("Expires", "0");

  }

  public function executeUpdate(sfWebRequest $request)
  {
    if (!$request->isMethod('POST'))
    {
      $this->redirect('course_student_mark/index');
    }

    $this->course = $this->getCourse();
    $this->course_subjects = $this->course->getCourseSubjectsForUser($this->getUser());
    $this->forms = $this->getForms($this->course_subjects);

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

      $this->getUser()->setFlash('notice', 'Las calificaciones se guardaron satisfactoriamente.');
      return $this->redirect(sprintf('@%s', $this->getUser()->getAttribute('referer_module', 'homepage')));
    }
    else
    {
      $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar calificar los alumnos. Por favor, intente nuevamente la operación.');
    }
    $this->setTemplate('index');

  }

  public function executeGoBack(sfWebRequest $request)
  {
    return $this->redirect(sprintf('@%s', $this->getUser()->getAttribute('referer_module', 'homepage')));

  }

  public function executeShowMarkChangeLog(sfWebRequest $request)
  {

    $this->mark = CourseSubjectStudentMarkPeer::retrieveByPK($request->getParameter('id'));

    return $this->renderPartial('show_change_log', array('mark' => $this->mark));

  }

}