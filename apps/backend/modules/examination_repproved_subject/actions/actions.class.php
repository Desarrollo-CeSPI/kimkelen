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

require_once dirname(__FILE__).'/../lib/examination_repproved_subjectGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/examination_repproved_subjectGeneratorHelper.class.php';

/**
 * examination_repproved_subject actions.
 *
 * @package    sistema de alumnos
 * @subpackage examination_repproved_subject
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class examination_repproved_subjectActions extends autoExamination_repproved_subjectActions
{
  /**
  * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
  *
  */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('examination_repproved'))
    {

      $this->getUser()->setFlash('warning', 'Debe seleccionar una mesa de previa para administrar las materias de la mesa.');
      $this->redirect('@examination_repproved');
    }

    $this->examination_repproved = ExaminationRepprovedPeer::retrieveByPK($this->getUser()->getReferenceFor('examination_repproved'));

    if (is_null($this->examination_repproved))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una mesa de previa para administrar las materias de la mesa.');
      $this->redirect('@examination_repproved');
    }

    parent::preExecute();
  }

  public function executeBack(sfWebRequest $request)
  {
    $this->redirect("@examination_repproved");
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->examination_repproved_subject = new ExaminationRepprovedSubject();
    $this->examination_repproved_subject->setExaminationRepproved($this->examination_repproved);

    $this->form = new ExaminationRepprovedSubjectForm($this->examination_repproved_subject);
    $this->form->setDefault('examination_repproved_subject_id', $this->examination_repproved->getId());
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->examination_repproved_subject = new ExaminationRepprovedSubject();
    $this->examination_repproved_subject->setExaminationRepproved($this->examination_repproved);

    $this->form = new ExaminationRepprovedSubjectForm($this->examination_repproved_subject);

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function getForms(ExaminationRepprovedSubject $examination_repproved_subject)
  {
    $forms = array();
    
    //agrego el orden alfabetico para el listado de alumnos.
    $criteria = new Criteria(ExaminationRepprovedSubjectPeer::DATABASE_NAME);
    $criteria->addJoin(StudentExaminationRepprovedSubjectPeer::STUDENT_REPPROVED_COURSE_SUBJECT_ID, StudentRepprovedCourseSubjectPeer::ID);
    $criteria->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID,  StudentPeer::ID);
    $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);
    
    foreach ($examination_repproved_subject->getStudentExaminationRepprovedSubjects($criteria) as $student_examination_repproved_subject)
    {
      $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getStudentExaminationRepprovedSubjectForm();
      $form = new $form_name($student_examination_repproved_subject);

      $form->getWidgetSchema()->setNameFormat("student_examination_repproved_subject_{$student_examination_repproved_subject->getId()}[%s]");
      $forms[$student_examination_repproved_subject->getId()] = $form;
    }

    return $forms;
  }

  public function executeCalifications(sfWebRequest $request)
  {
    $this->examination_repproved_subject = $this->getRoute()->getObject();

    $this->forms = $this->getForms($this->examination_repproved_subject);
  }

  public function executeUpdateCalifications(sfWebRequest $request)
  {
    $this->examination_repproved_subject = ExaminationRepprovedSubjectPeer::retrieveByPK($request->getParameter("id"));

    $this->forms = $this->getForms($this->examination_repproved_subject);

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
          $form->save();
          $this->examination_repproved_subject->saveCalificationsInRecord();
        }

        $this->getUser()->setFlash('notice', 'Las calificaciones se guardaron satisfactoriamente.');
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar calificar los alumnos. Por favor, intente nuevamente la operación.');
      }
    }
    $this->setTemplate('califications');
  }

  public function executeClose(sfWebRequest $request)
  {
    $this->examination_repproved_subject = $this->getRoute()->getObject();
  }

  public function executeRealClose(sfWebRequest $request)
  {
    $this->examination_repproved_subject = ExaminationRepprovedSubjectPeer::retrieveByPK($request->getParameter("id"));
    $this->examination_repproved_subject->close();

    $this->getUser()->setFlash("notice", "The examination repproved subject has been successfully closed.");
    $this->redirect("@examination_repproved_subject");
  }

  public function executeRealCloseNotAverageableCalifications(sfWebRequest $request)
  {
    $this->examination_repproved_subject = ExaminationRepprovedSubjectPeer::retrieveByPK($request->getParameter("id"));
    $this->examination_repproved_subject->closeNotAverageableCalifications();

    $this->getUser()->setFlash("notice", "The examination repproved subject has been successfully closed.");
    $this->redirect("@examination_repproved_subject");
  }

  public function executeStudents(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);
    $this->redirect('@student_examination_repproved_subject');

  }

  public function executeManageStudents(sfWebRequest $request)
  {

    $this->examination_repproved_subject = $this->getRoute()->getObject();

    if (null === $this->examination_repproved_subject)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una mesa de examen para inscribir a los estudiantes');

      $this->redirect('@examination_repproved_subject');
    }

    $this->form = new ExaminationRepprovedSubjectStudentForm($this->examination_repproved_subject);
  }

  public function executeUpdateStudents(sfWebRequest $request)
  {
    $this->examination_repproved_subject = ExaminationRepprovedSubjectPeer::retrieveByPk($request->getParameter('examination_repproved_subject[id]'));

    if (null === $this->examination_repproved_subject)
    {

      $this->getUser()->setFlash('error', 'Debe seleccionar una mesa de examen para inscribir a los estudiantes');

      $this->redirect('@examination_repproved_subject');
    }

    $this->form = new ExaminationRepprovedSubjectStudentForm($this->examination_repproved_subject);

    if ($request->isMethod("POST"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash('notice', 'Los alumnos seleccionados han sido correctamente inscriptos a la mesa de examen. En caso de haber generado el acta deberá regenerarla.');
      }
    }

    $this->setTemplate("manageStudents");
  }

    /**
   * Redefines parent::getPager because we need to add a custom parameter: career
   * used by _list_header partial
   *
   * @return sfPropelPager
  */
  public function getPager()
  {
        /* @var $pager sfPropelPager */
      $pager = parent::getPager();
      $pager->setParameter('examination', $this->examination_repproved);
      return $pager;
  }

 /* public function executePrintStudents(sfWebRequest $request)
  {
    $this->examination_repproved_subject = $this->getRoute()->getObject();
    $this->students = $this->examination_repproved_subject->getStudents();
    $this->previous_url = $request->getReferer();
    $this->setLayout('cleanLayout');

  }*/

  public function executeChangelogMarks(sfWebRequest $request)
  {
    $this->examination_repproved_subject = $this->getRoute()->getObject();
    
    if (null === $this->examination_repproved_subject)
    {
      $this->redirect($this->getModuleName().'/index');
    }
    
    $this->students = $this->examination_repproved_subject->getStudents();
    $this->previous_url = $this->getUser()->setAttribute('referer_module', 'examination_repproved_subject');

  }
  
  public function executeAssignPhysicalSheet(sfWebRequest $request)
  {
    try
    {
      // when GETting
      $this->examination_repproved_subject = $this->getRoute()->getObject();
     
    }
    catch (Exception $e)
    {
      // when POSTing
      $this->examination_repproved_subject = ExaminationRepprovedSubjectPeer::retrieveByPK($request->getParameter("id"));
    }
      
    $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->examination_repproved_subject->getId(), RecordType::EXAMINATION_REPPROVED);
    $this->books = BookPeer::retrieveActives();
    $this->forms= array();
    $this->record_sheet = $record->getRecordSheet();
    foreach ($record->getRecordSheets() as $rs)
    {
        $form = new RecordSheetForm($rs);
        $form->getWidgetSchema()->setNameFormat("record_sheet_{$rs->getId()}[%s]");
        $this->forms[$rs->getId()]= $form;
    }
      
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
          $form->save();
        }
        $this->getUser()->setFlash('notice', 'Los ítems fueron guardaron satisfactoriamente.');
        $this->redirect('@examination_repproved_subject'); 
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrieron algunos errores. Por favor, intente nuevamente la operación.');
      }
    }
      
  }
  
  public function executeGenerateRecord(sfWebRequest $request)
  {
    $examination_repproved_subject = $this->getRoute()->getObject();
    $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($examination_repproved_subject->getId(), RecordType::EXAMINATION_REPPROVED);
    if (!is_null($record))
    {
        $record->setStatus(RecordStatus::ANNULLED);
        $record->save();
    }
    $examination_repproved_subject->generateRecord(); 
    $this->getUser()->setFlash('info', 'El acta fue generada correctamente.');
    $this->redirect('@examination_repproved_subject');             
  }
  
  public function executePrintRecord(sfWebRequest $request)
  {
      $this->examination_subject = ExaminationRepprovedSubjectPeer::retrieveByPK($request->getParameter("id"));
      $this->record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->examination_subject->getId(), RecordType::EXAMINATION_REPPROVED);
      $this->setLayout('cleanLayout');
      $this->setTemplate('printRecord','examination_subject');
      
  }

  public function getFormsNotAverageableCalifications(ExaminationRepprovedSubject $examination_repproved_subject)
  {
    $forms = array();
    
    //agrego el orden alfabetico para el listado de alumnos.
    $criteria = new Criteria(ExaminationRepprovedSubjectPeer::DATABASE_NAME);
    $criteria->addJoin(StudentExaminationRepprovedSubjectPeer::STUDENT_REPPROVED_COURSE_SUBJECT_ID, StudentRepprovedCourseSubjectPeer::ID);
    $criteria->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID,  StudentPeer::ID);
    $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
    $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);
    
    foreach ($examination_repproved_subject->getStudentExaminationRepprovedSubjects($criteria) as $student_examination_repproved_subject)
    {
      $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getStudentExaminationRepprovedSubjectNotAverageableForm();
      $form = new $form_name($student_examination_repproved_subject);

      $form->getWidgetSchema()->setNameFormat("student_examination_repproved_subject_{$student_examination_repproved_subject->getId()}[%s]");
      $forms[$student_examination_repproved_subject->getId()] = $form;
    }

    return $forms;
  }
  
  public function executeNotAverageableCalifications(sfWebRequest $request)
  {
    $this->examination_repproved_subject = $this->getRoute()->getObject();

    $this->forms = $this->getFormsNotAverageableCalifications($this->examination_repproved_subject);
  }

   public function executeUpdateNotAverageableCalifications(sfWebRequest $request)
  {
    $this->examination_repproved_subject = ExaminationRepprovedSubjectPeer::retrieveByPK($request->getParameter("id"));

    $this->forms = $this->getFormsNotAverageableCalifications($this->examination_repproved_subject);

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
          $form->save();
          $this->examination_repproved_subject->saveCalificationsInRecord();
        }

        $this->getUser()->setFlash('notice', 'Las calificaciones se guardaron satisfactoriamente.');
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar calificar los alumnos. Por favor, intente nuevamente la operación.');
      }
    }
    $this->setTemplate('notAverageableCalifications');
  }

  public function executeCloseNotAverageableCalifications(sfWebRequest $request)
  {
    $this->examination_repproved_subject = $this->getRoute()->getObject();
  }

}
