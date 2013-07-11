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

require_once dirname(__FILE__).'/../lib/studentGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/studentGeneratorHelper.class.php';

/**
 * student actions.
 *
 * @package    sistema de alumnos
 * @subpackage student
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class studentActions extends autoStudentActions
{

  public function preExecute()
  {
    $this->getUser()->getAttributeHolder()->remove('tutors_from_student');
    parent::preExecute();
  }

/**
   * This action allows to manage career regsitration for one student
   * This is, add new registration as delete current ones
   *
   * @param sfWebRequest $request
   */
  public function executeRegisterForCareer($request)
  {
    $this->student = $this->getRoute()->getObject();
    $career_student = new CareerStudent();
    $career_student->setStudent($this->student);
    $class = SchoolBehaviourFactory::getInstance()->getFormFactory()->getRegisterStudentForCareerForm();
    $this->form = new $class($career_student);
  }
  /**
   * This action allows to delete a career regsitration for selected student
   * @see executeRegisterForCareer
   * @param sfWebRequest $request
   */
  public function executeDeleteRegistrationForCareer(sfWebRequest $request)
  {
    $career_student = CareerStudentPeer::retrieveByPK($request->getParameter('career_student_id'));
    if ( is_null($career_student))
    {
      $this->getUser()->setFlash('error', 'No career selected');
      $this->redirect('@student');
    }
    elseif ( $career_student->canBeDeleted())
    {

      $career_student->deleteStudentsCareerSubjectAlloweds();
      $career_student->deleteDivisionStudent();
      $career_student->deleteCourseSubjectStudent();
      $career_student->deleteStudentCareerSchoolYear();



      $career_student->delete();
      $this->getUser()->setFlash('info','The item was deleted successfully.');
    }
    else
    {
      $this->getUser()->setFlash('error', $career_student->getMessageCantBeDeleted());
    }
    $this->redirect('student/registerForCareer?id='.$career_student->getStudent()->getId());
  }

  /**
   * This action saves a new career registration for selected student
   * @see executeRegisterForCareer
   * @param sfWebRequest $request
   */
  public function executeUpdateRegistrationForCareer(sfWebRequest $request)
  {
    $this->student = StudentPeer::retrieveByPK($request->getParameter('id'));

    if (null === $this->student)
    {
      $this->getUser()->setFlash('error', 'No student selected');

      $this->redirect('@student');
    }
    $career_student = new CareerStudent();
    $career_student->setStudent($this->student);
    $class= SchoolBehaviourFactory::getInstance()->getFormFactory()->getRegisterStudentForCareerForm();
    $this->form = new $class($career_student);
    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      try
      {
        $career_student = $this->form->save();
      }
      catch (PropelException $e)
      {
        $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
      }

      $this->getUser()->setFlash('info','The item was updated successfully.');
      $this->redirect('student/registerForCareer?id='.$this->student->getId());
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
      $this->setTemplate('registerForCareer');
    }
  }

  /**
   * This action allows to manage SchoolYear registration for one student
   * This is, add new registration, change shift preference, or delete current one
   *
   * @param sfWebRequest $request
   */
  public function executeRegisterForCurrentSchoolYear(sfWebRequest $request)
  {
    $this->student = $this->getRoute()->getObject();
    $csy= SchoolYearPeer::retrieveCurrent();
    $school_year_student = $this->student->getSchoolYearStudentForSchoolYear($csy);
    if (is_null ($school_year_student))
    {
      $school_year_student = new SchoolYearStudent();
      $school_year_student->setStudent($this->student);
      $school_year_student->setSchoolYear($csy);
    }
    $this->form = new SchoolYearStudentForm($school_year_student);
  }

 /**
   * This action saves a new or created SchoolYear registration for selected student
   *
   * @see executeRegisterForCurrentSchoolYear
   * @param sfWebRequest $request
   */
  public function executeUpdateRegistrationForCurrentSchoolYear(sfWebRequest $request)
  {
    $this->student = StudentPeer::retrieveByPK($request->getParameter('student_id'));

    if (null === $this->student)
    {
      $this->getUser()->setFlash('error', 'No student selected');

      $this->redirect('@student');
    }
    $school_year_student = $this->student->getSchoolYearStudentForSchoolYear();
    if (is_null ($school_year_student))
    {
      $school_year_student = new SchoolYearStudent();
      $school_year_student->setStudent($this->student);
      $school_year_student->setSchoolYear(SchoolYearPeer::retrieveCurrent());
    }
    $this->form = new SchoolYearStudentForm($school_year_student);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $career_student = $this->form->save();
      $this->getUser()->setFlash('info','The item was updated successfully.');
      $this->redirect('@student');
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
      $this->setTemplate('registerForCurrentSchoolYear');
    }
  }
  /**
   * This action deletes a created SchoolYear registration for selected student
   *
   * @see executeRegisterForCurrentSchoolYear
   * @param sfWebRequest $request
   */
  public function executeDeleteRegistrationForCurrentSchoolYear(sfWebRequest $request)
  {
    if ($request->isMethod("POST"))
    {
      $s = SchoolYearStudentPeer::retrieveByPK($request->getParameter('school_year_student_id'));
      if ( !is_null ($s) )
      {
        $s->delete();
        $this->getUser()->setFlash('info','The item was deleted successfully.');
        $this->redirect('@student');
      }
    }
    $this->getUser()->setFlash('error',"The current school year student registration can't be deleted");
    $this->redirect('@student');
  }

  /**
   * This action activates person
   *
   * @param sfWebRequest $request
   */
  public function executePersonActivation(sfWebRequest $request)
  {
    $this->related_person = $this->getRoute()->getObject();
    $this->related_person->getPerson()->setIsActive(true);
    $this->related_person->save();
    $this->getUser()->setFlash('info','The item was updated successfully.');
    $this->redirect('@student');
  }

  /**
   * This action deactivates person
   *
   * @param sfWebRequest $request
   */
  public function executeDeactivate(sfWebRequest $request)
  {
    $this->related_person = $this->getRoute()->getObject();
    $this->related_person->getPerson()->setIsActive(false);
    $this->related_person->save();
    $this->getUser()->setFlash('info','The item was updated successfully.');
    $this->redirect('@student');
  }

  public function executeManageCareerSubjectAllowed(sfWebRequest $request)
  {
    $this->student = $this->getRoute()->getObject();
    /*
    $allowed = new StudentCareerSubjectAllowed();
    $allowed->setStudent($this->student);
    $this->form = new StudentCareerSubjectAllowedForm($allowed);
    */
    $this->form = new StudentCareerSubjectAllowedManagementForm($this->student);
  }

  public function executeUpdateCareerSubjectAllowed(sfWebRequest $request)
  {
    $this->student = StudentPeer::retrieveByPK($request->getParameter("student[id]"));

    if (is_null($this->student))
    {
      $this->getUser()->setFlash("error", "No student selected");
      $this->redirect("@student");
    }

    $this->form = new StudentCareerSubjectAllowedManagementForm($this->student);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash("notice", "The item was updated successfully.");
    }

    $this->redirect("student/manageCareerSubjectAllowed?id=".$this->student->getId());
  }

  /* batch actions */

  public function executeBatchRegisterForCareer(sfWebRequest $request, $objects)
  {
    $ids = array_map(create_function("\$o", "return \$o->getId();"), $objects);
    $this->getUser()->setAttribute("multiple_register_students_ids", implode(",", $ids));

    $this->redirect("student/multipleRegisterForCareer");
  }

  public function executeMultipleRegisterForCareer(sfWebRequest $request)
  {
    $this->title = "Career inscriptions";
    $this->help = "Only not registered students will be registered to career.";
    $this->url = 'student/multipleRegisterForCareer';

    $this->setTemplate("commonBatch");

    $ids = $this->getUser()->getAttribute("multiple_register_students_ids");
    $ids = explode(",", $ids);
    $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleCareerRegistrationForm();
    $this->form = new $form_name;
    $this->form->setStudentsIds($ids);

    if ($request->isMethod("post"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash("notice", "All students have been registered to the selected career.");
        $this->getUser()->getAttributeHolder()->remove("multiple_register_students_ids");

        $this->redirect("@student");
      }
    }
  }

  public function executeBatchRegisterForCurrentSchoolYear(sfWebRequest $request, $objects)
  {
    $ids = array_map(create_function("\$o", "return \$o->getId();"), $objects);
    $this->getUser()->setAttribute("multiple_register_students_ids", implode(",", $ids));

    $this->redirect("student/multipleRegisterForCurrentSchoolYear");
  }

  public function executeMultipleRegisterForCurrentSchoolYear(sfWebRequest $request)
  {
    $this->title = "Current school year inscriptions";
    $this->help = "Only not registered students will be registered to school year.";
    $this->url = 'student/multipleRegisterForCurrentSchoolYear';

    $this->setTemplate("commonBatch");

    $school_year = SchoolYearPeer::retrieveCurrent();

    $ids = $this->getUser()->getAttribute("multiple_register_students_ids");
    $ids = explode(",", $ids);
    $this->form = new MultipleSchoolYearRegistrationForm();
    $this->form->setDefault("school_year_id", $school_year->getId());
    $this->form->setStudentsIds($ids);

    if ($request->isMethod("post"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash("notice", "All students have been registered to current school year.");
        $this->getUser()->getAttributeHolder()->remove("multiple_register_students_ids");

        $this->redirect("@student");
      }
    }
  }

  public function executeHistory(sfWebRequest $request)
  {
    $this->career_student = CareerStudentPeer::retrieveByPK($request->getParameter("career_student_id"));
    $this->redirectUnless($this->career_student, "@student");
  }

  public function executeHistoryDetails(sfWebRequest $request)
  {
    $this->career_student = CareerStudentPeer::retrieveByPK($request->getParameter("career_student_id"));
    $this->redirectUnless($this->career_student, "@student");

    $this->course_subject_student = CourseSubjectStudentPeer::retrieveByPK($request->getParameter("course_subject_student_id"));
    $this->redirectUnless($this->course_subject_student, "@student");

    $back_url = $request->getParameter('back_url');

    $this->back_url = !is_null($back_url) ? $back_url . '?id=' . $this->career_student->getStudentId() : "student/history?career_student_id=". $this->career_student->getId();

  }


  public function executeAnalytical(sfWebRequest $request)
  {
    $this->career_student = CareerStudentPeer::retrieveByStudent($request->getParameter("id"));
  }
  public function executePrintAnalytical(sfWebRequest $request)
  {
    $this->career_student = CareerStudentPeer::retrieveByPK($request->getParameter("id"));
    $this->setLayout('cleanLayout');
  }

  public function executeStudentCoursesRegularity(sfWebRequest $request)
  {
    $this->student = StudentPeer::retrieveByPK($request->getParameter('id'));
    if(empty($this->student))
      $this->redirect('@student');
    $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getStudentCoursesRegularityForm();
    $this->form = new $form_name;
    $this->form->setStudent($this->student);
    if ($request->isMethod("post"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash("notice", "All courses states have been saved successfully.");

      }
    }

  }

  public function executeManageBrothers()
  {
    $this->student = $this->getRoute()->getObject();
    $this->form = new StudentBrothersForm($this->student);
  }

  public function executeUpdateBrothers(sfWebRequest $request)
  {
    if (!$request->isMethod("post"))
    {
      $this->redirect('student/index');
    }

    $this->student = StudentPeer::retrieveByPK($request->getParameter('id'));
    $this->form = new StudentBrothersForm($this->student);
    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();
      $this->getUser()->setFlash('notice', 'Los hermanos del alumno se guardaron satisfactoriamente.');
    }
    else
    {
      $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar guardar los hermanos del alumno. Por favor, intente nuevamente la operación.');
    }
    $this->setTemplate('manageBrothers');
  }

  public function executeTutors(sfWebRequest $request)
  {
//    $this->getUser()->setReferenceFor($this);
//    $this->getUser()->setAttribute('tutors_from_student', true);
    $this->forward('tutor', 'indexByStudent');
  }

  public function executeSanctions(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);

    $this->redirect("@student_disciplinary_sanction");
  }

  public function executeChangeOrientation(sfWebRequest $request)
  {
    $this->student = $this->getRoute()->getObject();
    $this->form = new StudentOrientationForm($this->student);
  }

  public function executeUpdateOrientation(sfWebRequest $request)
  {
    if (!$request->isMethod("post"))
    {
      $this->redirect('student/index');
    }

    $this->student = StudentPeer::retrieveByPK($request->getParameter('id'));
    $this->form = new StudentOrientationForm($this->student);
    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();
      $this->getUser()->setFlash('notice', 'La orientación se guardo satisfactoriamente.');
      $this->redirect('@student');
    }
    else
    {
      $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar guardar la orientación, intente nuevamente');
      $this->setTemplate('changeOrientation');
    }

  }

  public function executeFree(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);

    $this->redirect("student_free");
  }

  public function executeReincorporation(sfWebRequest $request)
  {
    $this->getUser()->setReferenceFor($this);

    $this->redirect("@student_reincorporation");
  }

  public function executeEditCourseSubjectStudentHistory(sfWebRequest $request)
  {

    if ($request->isMethod("post"))
    {
      $course_subject_student_request = $request->getParameter('course_subject_student');
      $pk = $course_subject_student_request['id'];
    }
    else
    {
      $pk = $request->getParameter('course_subject_student_id');
    }

    $this->course_subject_student = CourseSubjectStudentPeer::retrieveByPK($pk);

    $this->student = $this->course_subject_student->getStudent();

    $this->form = new StudentEditHistoryForm($this->course_subject_student);

    $this->back_to = $this->form->getBackTo();

    if ($request->isMethod("post"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

      if ($this->form->isValid())
      {
        $this->form->save();
        $this->redirect('student/editCourseSubjectStudentHistory?course_subject_student_id=' . $pk);
      }
    }
  }

  public function executeBackToPreviousCourseSubjectStatus(sfWebRequest $request)
  {
    $this->course_subject_student = CourseSubjectStudentPeer::retrieveByPK($request->getParameter("course_subject_student_id"));

    if (is_null($this->course_subject_student))
    {
      $this->redirect('@student');
    }

    $this->course_subject_student->backToPreviousCourseSubjectStatus();
    $this->redirect("student/editCourseSubjectStudentHistory?course_subject_student_id=" . $this->course_subject_student->getId());
  }

  public function executePrintReportCard(sfWebRequest $request)
  {
    $this->student = $this->getRoute()->getObject();
    $this->student_career_school_years = $this->student->getStudentCareerSchoolYears();
  }

  public function executeShowAssistanceAndSanctionReport($request)
  {
    $this->student = $this->getRoute()->getObject();
    $this->student_career_school_years = $this->student->getCurrentStudentCareerSchoolYears();
    $this->setLayout('cleanLayout');
  }

  public function executePrintDetails()
  {
    $this->setLayout('cleanLayout');
    $this->student = $this->getRoute()->getObject();
  }

  public function executeWithdrawStudent()
  {
    $student = $this->getRoute()->getObject();
    $student->getCurrentStudentCareerSchoolYear()->setStatus(StudentCareerSchoolYearStatus::WITHDRAWN);
    $student->getCurrentStudentCareerSchoolYear()->save();

    $this->getUser()->setFlash('info','The item was updated successfully.');
    $this->redirect('@student');
  }

  public function executeUndoWithdrawStudent(){
    $student = $this->getRoute()->getObject();
    $student->getCurrentStudentCareerSchoolYear()->setStatus(StudentCareerSchoolYearStatus::IN_COURSE);
    $student->getCurrentStudentCareerSchoolYear()->save();

    $this->getUser()->setFlash('info','The item was updated successfully.');
    $this->redirect('@student');
  }
}