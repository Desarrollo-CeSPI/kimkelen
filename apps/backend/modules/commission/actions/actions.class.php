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

require_once dirname(__FILE__) . '/../lib/commissionGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/commissionGeneratorHelper.class.php';

/**
 * commission actions.
 *
 * @package    sistema de alumnos
 * @subpackage commission
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class commissionActions extends autoCommissionActions
{

  public function executeBackPeriod(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    $this->course = $this->getRoute()->getObject();
    $this->course->backPeriod();
    $this->getUser()->setFlash('notice', __('The course has back to last period succesfuly.'));
    $this->redirect('@commission');

  }

  public function executeStudents(sfWebRequest $request)
  {
    $reference_array = array(
      "peer" => "CourseSubjectStudentPeer",
      "fk" => "COURSE_SUBJECT_ID",
      "object_ids" => $this->getRoute()->getObject()->getCourseSubjectIds(),
      "back_to" => "@commission",
      "title" => "Commission student list",
      "back_to_label" => "Return to commission list"
    );

    $this->getUser()->setReferenceFor($this, $reference_array, "shared_student");

    $this->redirect('@shared_student');

  }

  public function executeCourseTeachers(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->form = new CourseTeachersForm($this->course);

  }

  public function executeUpdateTeachers(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPk($request->getParameter('id'));

    if (null === $this->course)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una comisión para configurar sus profesores');

      $this->redirect('@commission');
    }

    $this->form = new CourseTeachersForm($this->course);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {

      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los profesores seleccionados fueron exitosamente asignados a la comisión.');
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
    $this->back_url= '@commission';

  }

  public function executeUpdatePreceptors(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPk($request->getParameter('id'));

    if (null === $this->course)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una comisión para configurar sus preceptores');

      $this->redirect('@commission');
    }

    $this->form = new CoursePreceptorsForm($this->course);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los preceptores seleccionados han sido correctamente asignados a la comisión.');
      $this->back_url = '@commission';
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }

    $this->setTemplate('preceptors');

  }

  public function executeCourseSubjectStudent(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "commission");

    $this->forward("shared_course", "courseSubjectStudent");
  }

  public function executeCopyStudentsFromOtherCourse(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->form = new CopyStudentsFromOtherCourseForm($this->course);
  }

  public function executeUpdateStudents(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPk($request->getParameter('id'));

    if (null === $this->course)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una comisión para copiar los alumnos de otra comisión');

      $this->redirect('@commission');
    }

    $this->form = new CopyStudentsFromOtherCourseForm($this->course);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los alumnos de la comisión seleccionada han sido correctamente asignados a la comisión.');
      $this->redirect('@commission');
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }

    $this->setTemplate('copyStudentsFromOtherCourse');
  }

  public function executeCommissionSubjectStudentsRegularity(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPK($request->getParameter('id'));
    if (empty($this->course))
      $this->redirect('@commission');
    $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCourseSubjectStudentsRegularityForm();
    $this->form = new $form_name;
    $this->form->setCourse($this->course);
    if ($request->isMethod("post"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();
        $this->getUser()->setFlash("notice", "All students states have been saved successfully.");
      }
    }

  }

  public function executeCalifications(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();

    $this->getUser()->setAttribute("referer_module", "commission");

    $this->redirect("course_student_mark/index?id=" . $this->course->getId());

  }

  public function executeClose(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "commission");

    $this->forward("shared_course", "close");

  }

  public function executeManageCourseDays(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "commission");

    $this->forward("shared_course", "manageCourseDays");

  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $this->getProcessFormNotice($form->getObject()->isNew());

      $course = $form->save();

      if ($this->getUser()->isPreceptor())
      {
        $course->addPreceptor($this->getUser());
      }


      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $course)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->setProcessFormSaveAndAddFlash($notice);

        $this->redirect('@commission_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        if ($request->hasParameter('_save_and_list'))
        {
          $this->redirect('@commission');
        }
        else
        {
          $this->redirect('@commission_edit?id=' . $course->getId());
        }
      }
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }

  }

  public function executePrintCalification(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->getUser()->setAttribute("referer_module", "commission");
    $this->redirect("course_student_mark/print?id=" . $this->course->getId());

  }

  public function executeCourseConfiguration(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->getUser()->setAttribute("referer_module", "commission");
    $this->redirect("shared_course/courseConfiguration?id=" . $this->course->getId());

  }

  public function executeRelatedToDivision(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPk($request->getParameter('id'));

    if (null === $this->course)
    {
      $this->course = $this->getRoute()->getObject();
    }
    $this->form = new CourseRelatedToDivisionForm($this->course);

    if ($request->isMethod("post"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

      if ($this->form->isValid())
      {
        $this->form->save();
        $this->getUser()->setFlash("notice", "The course has been related to the division succesfully.");
      }
    }

  }

  public function executeAttendanceSheetByCourseSubject(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->getUser()->setAttribute("referer_module", "commission");
    $this->redirect("shared_course/attendanceSheetByCourseSubject?id=" . $this->course->getId());
  }

  public function executeAttendanceSubject(sfWebRequest $request)
  {
    $this->redirectIf($this->getUser()->isTeacher());
    $course = $this->getRoute()->getObject();
    if (count($course->getCourseSubjects()) > 1){
      $course_id = $course->getId();
      $this->redirect("student_attendance/MultipleSubjectsCommissionAttendance?course=$course_id&division_id=");
    }
    else {
      $career_school_year_id = $course->getCareerSchoolYear()->getId();
      $course_subject_id = array_shift($course->getCourseSubjectIds());
      $year = $course->getYear();
      $this->redirect("student_attendance/StudentAttendance?url=commission&year=$year&course_subject_id=$course_subject_id&career_school_year_id=$career_school_year_id&division_id=");
    }
  }

  public function executeShowStudents(sfWebRequest $request)
  {
    $course = CoursePeer::retrieveByPK($request->getParameter('id'));

    return $this->renderPartial('course_students', array('course' => $course));
  }

  public function executeCalificateNonNumericalMark(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->getUser()->setAttribute("referer_module", "commission");
    $this->redirect("course_student_mark/calificateNonNumericalMark?id=" . $this->course->getId());
  }

  public function executeAddSubject(sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      $params = $request->getPostParameters();
      $this->course = CoursePeer::retrieveByPk($params['course']['id']);
      $this->form = new SubjectForCommissionForm($this->course);
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        try
        {
          $this->form->save();
          $this->getUser()->setFlash("notice", "New subject added to commission successfully");
          $this->redirect("@commission");
        }
        catch (PropelException $e)
        {
          $this->getUser()->setFlash('error', 'La materia ya existe en la comisión.');
        }
        
      }
    }
    else
    {
      $this->course = $this->getRoute()->getObject();
      $this->course_subjects = $this->course->getCourseSubjects();
      $this->form = new SubjectForCommissionForm($this->course);
    }
  }

  public function executeDeleteSubject(sfWebRequest $request)
   {
     $cs= CourseSubjectPeer::retrieveByPK($request->getParameter('course_subject_id'));

      try {
        $cs->delete();
        $this->getUser()->setFlash("notice", "The item was deleted successfully.");
      } catch (PropelException $e) {
        $this->getUser()->setFlash('error', 'A problem occurs when deleting the selected items.');
      }
     $this->redirect("@commission");
   }

  public function executeChangelogMarks(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();

    if (null === $this->course)
    {
      $this->redirect($this->getModuleName().'/index');
    }

    $this->getUser()->setAttribute('referer_module', 'commission');

    $this->redirect('course_student_mark/changelogMarks?id='.$this->course->getId());
  }
  
  public function executeRevertCalificateNonNumericalMark(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->getUser()->setAttribute("referer_module", "commission");
    $this->redirect("course_student_mark/revertCalificateNonNumericalMark?id=" . $this->course->getId());
  }
  
  public function executeGenerateRecord(sfWebRequest $request)
  {
      $this->course = $this->getRoute()->getObject();
      $this->course_subjects = $this->course->getCourseSubjects();
      $this->url = 'commission';
      
      if (count($this->course_subjects) == 1)
      {
          $this->getUser()->setAttribute("referer_module", "commission");
          $this->redirect("course_student_mark/generateRecord?course_subject_id=" . $this->course->getCourseSubject()->getId());
      }
      
      
  }
  
  public function executeAssignPhysicalSheet(sfWebRequest $request)
  {
      $this->course = $this->getRoute()->getObject();
      $this->course_subjects = $this->course->getCourseSubjects();
      $this->title = 'Assign physical sheet';
      $this->action = 'assignPhysicalSheet';
      $this->url = 'commission';
      
      if (count($this->course_subjects) == 1)
      { 
          $this->getUser()->setAttribute("referer_module", "commission");
          $this->redirect("course_student_mark/assignPhysicalSheet?course_subject_id=" . $this->course->getCourseSubject()->getId());
      }
      
  }
  
  public function executePrintRecord(sfWebRequest $request)
  {
      $this->course = $this->getRoute()->getObject();
      $this->course_subjects = $this->course->getCourseSubjects();
      $this->url = 'commission';
      $this->title = 'Print record';
      $this->action = 'printRecord';
      
      if (count($this->course_subjects) == 1)
      { 
          $this->getUser()->setAttribute("referer_module", "commission");
          $this->redirect("course_student_mark/printRecord?course_subject_id=" . $this->course->getCourseSubject()->getId());
      }
      
      $this->setTemplate('assignPhysicalSheet','commission');
      
  }
   
}
