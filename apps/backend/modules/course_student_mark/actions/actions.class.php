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
    else if (!$course->canEditMarks() && !$course->getIsPathway())
    {
      $this->getUser()->setFlash('error', 'El curso seleccionado no permite la edición de notas: o bien no tiene alumnos o bien el año lectivo al que pertenece no permite la edición de calificaciones.');

      $this->redirect('@course');
    }

    return $course;

  }

  protected function getForms($course_subjects, $is_pathway)
  {
    $forms = array();

    foreach ($course_subjects as $course_subject)
    {
      
      if ($is_pathway){
	      $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCourseSubjectPathwayMarksForm();
      } else {
	      $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCourseSubjectMarksForm();
      }
      $forms[$course_subject->getId()] = new $form_name($course_subject);
    }

    return $forms;

  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->course = $this->getCourse();
    $this->course_subjects = $this->course->getCourseSubjectsForUser($this->getUser());
    $this->forms = $this->getForms($this->course_subjects, $this->course->getIsPathway());

  }

  public function executePrint(sfWebRequest $request)
  {

    $this->setLayout('cleanLayout');

    $this->course = CoursePeer::retrieveByPK($this->getRequest()->getParameter("id"));

    $this->course_subjects = $this->course->getCourseSubjectsForUser($this->getUser());

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
    $this->forms = $this->getForms($this->course_subjects, $this->course->getIsPathway());

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
        
        if($this->getCourse()->getIsPathway())
        {
            foreach($this->course_subjects as $cs)
            {
                $cs->saveCalificationsInRecord();
            }
        }
      }

     //Para el caso de las observaciones finales
      $course_subjects = $this->getCourse()->getCourseSubjects();
      $all_closed = true;
      foreach ($course_subjects as $cs)
      { $calification_final = 0;
        foreach($cs->getCourseSubjectStudents() as $css)
        {
            if(!is_null($css->getObservationFinal()))
            {
               $calification_final ++;
            }
        }
        $result = false;
        if($calification_final == count($cs->getCourseSubjectStudents()))
        {
            $result = true;
        }
        $all_closed = $all_closed && $result;
      }

      if ($all_closed)
      {
        $course = $this->getCourse()->setIsClosed(true);
        $course->save(); 

      }
      //FIN para el caso de las observaciones finales

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

  public function executeCalificateNonNumericalMark(sfWebRequest $request)
  {
    $this->course = $this->getCourse();
    $this->course_subject = $this->course->getCourseSubject();
    $this->form = new CourseSubjectNonNumericalCalificationsForm($this->course_subject);
    $this->back_url = $this->getUser()->getAttribute('referer_module');

  }

  public function executeSaveCalificateNonNumericalMark(sfWebRequest $request)
  {
    if ($request->isMethod('POST'))
    {
      $params = $request->getParameter('course_subject_non_numerical_califications');
      $this->course_subject = CourseSubjectPeer::retrieveByPk($params['course_subject_id']);
      $this->form = new CourseSubjectNonNumericalCalificationsForm($this->course_subject);

      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash('notice', 'Se han eximido a los alumnos seleccionados satisfactoriamente.');
        return $this->redirect(sprintf('@%s', $this->getUser()->getAttribute('referer_module', 'homepage')));
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar eximir a los alumnos. Por favor, intente nuevamente la operación.');
        $this->course = $this->course_subject->getCourse();
        $this->back_url = $this->getUser()->getAttribute('referer_module');
      }
      $this->setTemplate('calificateNonNumericalMark');
    }
  }

  public function executeChangelogMarks(sfWebRequest $request)
  {
    $this->previous_url = $request->getReferer();
    $this->course = CoursePeer::retrieveByPK($this->getRequest()->getParameter("id"));
    $this->course_subjects = $this->course->getCourseSubjectsForUser($this->getUser());
  }
  
  public function executeRevertCalificateNonNumericalMark(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPK($this->getRequest()->getParameter("id"));
    $this->course_subject = $this->course->getCourseSubject();
    $this->form = new RevertCourseSubjectNonNumericalCalificationsForm($this->course_subject);
    $this->back_url = $this->getUser()->getAttribute('referer_module');

  }
 
  public function executeSaveRevertCalificateNonNumericalMark(sfWebRequest $request)
  {
    if ($request->isMethod('POST'))
    {
      $params = $request->getParameter('revert_course_subject_non_numerical_califications');
      $this->course_subject = CourseSubjectPeer::retrieveByPk($params['course_subject_id']);
      $this->form = new RevertCourseSubjectNonNumericalCalificationsForm($this->course_subject);

      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash('notice', 'Se han deseximido a los alumnos seleccionados satisfactoriamente.');
        return $this->redirect(sprintf('@%s', $this->getUser()->getAttribute('referer_module', 'homepage')));
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar deseximir a los alumnos. Por favor, intente nuevamente la operación.');
        $this->course = $this->course_subject->getCourse();
        $this->back_url = $this->getUser()->getAttribute('referer_module');
      }
      $this->setTemplate('revertCalificateNonNumericalMark');
    }
  }
  
  public function executePrintSubjectCalification(sfWebRequest $request)
  {
    $this->setLayout('cleanLayout');
    $this->course_subjects = array( CourseSubjectPeer::retrieveByPK($this->getRequest()->getParameter("id")));
    $this->setTemplate('print');
 
  }
  
  public function executeAssignPhysicalSheet(sfWebRequest $request)
  {
    $this->cs = CourseSubjectPeer::retrieveByPK($request->getParameter('course_subject_id'));
      
    $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->cs->getId(), RecordType::COURSE);
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
        return $this->redirect(sprintf('@%s', $this->getUser()->getAttribute('referer_module', 'homepage')));
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrieron algunos errores. Por favor, intente nuevamente la operación.');
      }
    }
  }
  
  public function executeGenerateRecord(sfWebRequest $request)
  {
        $cs = CourseSubjectPeer::retrieveByPK($request->getParameter('course_subject_id'));
        $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($cs->getId(), RecordType::COURSE);
        if (!is_null($record))
        {
            $record->setStatus(RecordStatus::ANNULLED);
            $record->save();
        }
        if($cs->getCourse()->isPathway())
        {
            $cs->generateRecordPathway();
        }
        else
        {
            $cs->generateRecord();
        }
        
        $this->getUser()->setFlash('info', 'El acta fue generada correctamente.');
        return $this->redirect(sprintf('@%s', $this->getUser()->getAttribute('referer_module', 'homepage')));
              
  }
  
  public function executePrintRecord(sfWebRequest $request)
  {
      $this->cs = CourseSubjectPeer::retrieveByPK($request->getParameter('course_subject_id'));
      $this->record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->cs->getId(), RecordType::COURSE);
      $this->setLayout('cleanLayout');
      
  }
  
  public function executeNotAverageableCalifications(sfWebRequest $request)
  {
    $this->course = $this->getCourse();
    $this->course_subjects = $this->course->getCourseSubjectsForUser($this->getUser());
    
    $forms = array();

    foreach ($this->course_subjects as $course_subject)
    {
      
        $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCourseSubjectNotAverageableMarksForm();
        $forms[$course_subject->getId()] = new $form_name($course_subject);
    }
    
    $this->forms = $forms;

  }
  
  
  public function executeUpdateNotAverageable(sfWebRequest $request)
  {
    if (!$request->isMethod('POST'))
    {
      $this->redirect('course_student_mark/notAverageableCalifications');
    }

    $this->course = $this->getCourse();
    $this->course_subjects = $this->course->getCourseSubjectsForUser($this->getUser());
    
    foreach ($this->course_subjects as $course_subject)
    {
      
        $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCourseSubjectNotAverageableMarksForm();
        $forms[$course_subject->getId()] = new $form_name($course_subject);
    }
    
    $this->forms = $forms;
    
    
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
      
      
      //Para cerrar curso
      $course_subjects = $this->getCourse()->getCourseSubjects();
      $all_closed = true;
      foreach ($course_subjects as $cs)
      { $calification_final = 0;
        foreach($cs->getCourseSubjectStudentsNotAverageable() as $css)
        {
            if(!is_null($css->getNotAverageableCalification()))
            {
               $calification_final ++;
            }
        }
        $result = false;
        
        if($calification_final == count($cs->getCourseSubjectStudentsNotAverageable()))
        {
            $result = true;
        }
        $all_closed = $all_closed && $result;
      }

      if ($all_closed)
      {
        //llevo el periodo al último
            
        $last_period =  $this->getCourse()->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration()->getCourseMarks();
        $this->course->setCurrentPeriod($last_period); 
        $this->course->save();
            
      }
      //FIN cerrar curso

      $this->getUser()->setFlash('notice', 'Las calificaciones se guardaron satisfactoriamente.');
      return $this->redirect(sprintf('@%s', $this->getUser()->getAttribute('referer_module', 'homepage')));
    }
    else
    {
      $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar calificar los alumnos. Por favor, intente nuevamente la operación.');
    }
    $this->setTemplate('index');

  }
  
  
    
}
