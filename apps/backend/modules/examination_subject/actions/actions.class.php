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

require_once dirname(__FILE__) . '/../lib/examination_subjectGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/examination_subjectGeneratorHelper.class.php';

/**
 * examination_subject actions.
 *
 * @package    sistema de alumnos
 * @subpackage examination_subject
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class examination_subjectActions extends autoExamination_subjectActions
{

  public function executeBack(sfWebRequest $request)
  {
    $this->redirect("@examination");

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
    $this->redirect("@examination_subject");

  }

  /**
   * restrictions
   */
  public function executeNew(sfWebRequest $request)
  {
    $this->getUser()->setFlash("error", "Examination subjects can't be created directly.");
    $this->redirect("@examination_subject");

  }

  public function executeShow(sfWebRequest $request)
  {
    $this->redirect("@examination_subject");

  }

  public function executeStudents(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);
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

        $this->redirect('@examination_subject');
      }
    }


    $this->form = new ExaminationSubjectStudentForm($this->examination_subject);

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
    $examination = ExaminationPeer::retrieveByPK($this->getUser()->getReferenceFor('examination'));
        /* @var $pager sfPropelPager */
      $pager = parent::getPager();
      $pager->setParameter('examination', $examination);
      return $pager;
  }
  
  public function executeChangelogMarks(sfWebRequest $request)
  {
    $this->examination_subject = $this->getRoute()->getObject();
   
    if (null === $this->examination_subject)
    {
      $this->redirect($this->getModuleName().'/index');
    }

    if (is_null($request->getReferer()))
    {
      $this->previous_url = $this->getUser()->setAttribute('referer_module', 'examination_subject');
    }
    else
    {
      $this->previous_url = $request->getReferer();
    }

     $this->students = $this->examination_subject->getStudents();
  }
                        
  public function executeAssignPhysicalSheet(sfWebRequest $request)
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
      
    $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($this->examination_subject->getId(), RecordType::EXAMINATION);
    $this->books = BookPeer::retrieveActives();
    $this->forms= array();
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
      }
      else
      {
        $this->getUser()->setFlash('error', 'Ocurrieron algunos errores. Por favor, intente nuevamente la operación.');
      }
    }
      
  }
  
  public function executeGenerateRecord(sfWebRequest $request)
  {
      //calculo cantidad de hojas: cantidad de alumnos / cantidad de renglones por hoja.
   /*   $examination_subject = $this->getRoute()->getObject();
      $setting = SettingParameterPeer::retrieveByName(BaseSchoolBehaviour::LINES_EXAMINATION);
      $sheets = $examination_subject->countTotalStudents() / $setting->getValue();
      $sheets = ceil($sheets);
    */

       $con =  Propel::getConnection();
       
       try
       {  
            $examination_subject = $this->getRoute()->getObject();
            $setting = SettingParameterPeer::retrieveByName(BaseSchoolBehaviour::LINES_EXAMINATION);

            $r = new Record();
            $r->setRecordType(RecordType::EXAMINATION);
            $r->setCourseOriginId($examination_subject->getId());
            $r->setLines($setting->getValue());
            $r->setStatus(RecordStatus::ACTIVE); 
            $r->setUsername(sfContext::getInstance()->getUser());
            $r->save();

            $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($examination_subject->getId(), RecordType::EXAMINATION);

            $i = 1;
            $sheet =1;
            $record_sheet = new RecordSheet();
            $record_sheet->setRecord($record);
            $record_sheet->setSheet($sheet);
            $record_sheet->save();

            foreach ($examination_subject->getSortedByNameCourseSubjectStudentExaminations() as $csse)
            {
               $rd = new RecordDetail();
               $rd->setRecordId($record->getId());
               $rd->setStudent($csse->getCourseSubjectStudent()->getStudent());
               $rd->setMark($csse->getMark());
               $rd->setIsAbsent($csse->getIsAbsent());
               if ($csse->getMark() < SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNote())
               {
                   $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getDisapprovedResult());
               }
               else
               {
                   $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getApprovedResult());
               }

               $rd->setLine($i);

               if ($i > ($sheet * $record->getLines()))
               {
                   $sheet ++;
                   $record_sheet = new RecordSheet();
                   $record_sheet->setRecord($record);
                   $record_sheet->setSheet($sheet);
                   $record_sheet->save();

               }
               $rd->setSheet($sheet);
               $i++;

               $rd->save();

               ####Liberando memoria###
               $rd->clearAllReferences(true);
               unset($rd);
               ##################*/
            }
            
            $con->commit();
        echo "listo";die();
       }
       catch (Exception $e)
       {
          $con->rollBack();
          $this->getUser()->setFlash('error', 'Ocurrió un error y no se guardaron los cambios.');
          $this->redirect('@examination_subject');
       }
              
  }
}