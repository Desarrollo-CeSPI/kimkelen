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

require_once dirname(__FILE__) . '/../lib/manual_examination_subjectGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/manual_examination_subjectGeneratorHelper.class.php';

/**
 * examination_subject actions.
 *
 * @package    sistema de alumnos
 * @subpackage examination_subject
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class manual_examination_subjectActions extends autoManual_examination_subjectActions
{

  public function executeBack(sfWebRequest $request)
  {
    $this->redirect("@manual_examination");

  }

  public function getForms($examination_subject, $show)
  {
    $forms = array();
    $c = new Criteria();

    foreach ($examination_subject->getSortedCourseSubjectStudentExaminations($c) as $course_subject_student_examination)
    {
      $form = new CourseSubjectStudentExaminationForm($course_subject_student_examination);
      $form->getWidgetSchema()->setNameFormat("course_subject_student_examination_{$course_subject_student_examination->getId()}[%s]");
      $forms[$course_subject_student_examination->getId()] = $form;
    }

    return $forms;

  }

  public function executeCalifications(sfWebRequest $request)
  {
    try
    {
      // when GETting
      $this->examination_subject = $this->getRoute()->getObject();
    }
    catch (Exception $e)
    {
      // when POSTing
      $this->examination_subject = ExaminationSubjectPeer::retrieveByPK($request->getParameter("id"));
    }
    $show = $this->examination_subject->getIsClosed();

    $this->forms = $this->getForms($this->examination_subject, $show);

    if ($request->isMethod("post"))
    {
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
          $form->getObject()->setCanTakeExamination(true);
          $form->save();
        }

        $this->getUser()->setFlash('notice', 'Las calificaciones se guardaron satisfactoriamente.');
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar calificar los alumnos. Por favor, intente nuevamente la operación.');
      }
    }

  }

  public function executeClose(sfWebRequest $request)
  {
    $this->examination_subject = $this->getRoute()->getObject();

  }

  public function executeRealClose(sfWebRequest $request)
  {
    $this->examination_subject = ExaminationSubjectPeer::retrieveByPK($request->getParameter("id"));
    $this->examination_subject->close();

    $this->getUser()->setFlash("notice", "The examination subject has been successfully closed.");
    $this->redirect("@manual_examination_subject");

  }

  public function executeShow(sfWebRequest $request)
  {
    $this->redirect("@manual_examination_subject");

  }

  public function executeStudents(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);
    $this->getUser()->setAttribute('is_manual_examination_subject', true);
    $this->redirect('@course_subject_student_examination');
  }

  public function executeManageStudents(sfWebRequest $request)
  {
    $this->examination_subject = ExaminationSubjectPeer::retrieveByPk($request->getParameter('examination_subject[id]'));

    if (null === $this->examination_subject)
    {
      $this->examination_subject = $this->getRoute()->getObject();
      if (null === $this->examination_subject)
      {
        $this->getUser()->setFlash('error', 'Debe seleccionar una mesa de examen para inscribir a los estudiantes');

        $this->redirect('@manual_examination_subject');
      }
    }


    $this->form = new ManualExaminationSubjectStudentForm($this->examination_subject);

    if ($request->isMethod("post"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash('notice', 'Los alumnos seleccionados han sido correctamente inscriptos a la mesa de examen.');
      }
    }

  }

  public function executePrintStudents(sfWebRequest $request)
  {
    /* @var $examination_subject ExaminationSubject */
    $this->examination_subject = $this->getRoute()->getObject();
    $this->students = $this->examination_subject->getStudents();
    $this->previous_url = $request->getReferer();
    $this->setLayout('cleanLayout');

  }

  /**
   * Redefines parent::getPager because we need to add a custom parameter: career
   * used by _list_header partial
   *
   * @return sfPropelPager
  */
  public function getPager()
  {
    $examination = ExaminationPeer::retrieveByPK($this->getUser()->getReferenceFor('manual_examination'));
        /* @var $pager sfPropelPager */
      $pager = parent::getPager();
      $pager->setParameter('manual_examination', $examination);
      return $pager;
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = $this->configuration->getForm();
    $this->form->setDefault('examination_id', $this->getUser()->getReferenceFor('manual_examination'));
    $this->examination_subject = $this->form->getObject();
  }

  public function executeChangelogMarks(sfWebRequest $request)
  {
    $this->examination_subject = $this->getRoute()->getObject();

    if (null === $this->examination_subject)
    {
      $this->redirect($this->getModuleName().'/index');
    }

    $this->getUser()->setAttribute('referer_module', 'manual_examination_subject');

    $this->redirect('examination_subject/changelogMarks?id='.$this->examination_subject->getId());
  }

}