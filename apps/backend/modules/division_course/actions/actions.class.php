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

require_once dirname(__FILE__).'/../lib/division_courseGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/division_courseGeneratorHelper.class.php';

/**
 * division_course actions.
 *
 * @package    sistema de alumnos
 * @subpackage division_course
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class division_courseActions extends autoDivision_courseActions
{
  /**
   * redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');

    if ($this->getUser()->getReferenceFor('division'))
    {
      $this->division = DivisionPeer::retrieveByPK($this->getUser()->getReferenceFor('division'));

      if ( is_null($this->division))
      {
        $this->getUser()->setFlash('warning', __('Must select a division to manage ther courses.'));
        $this->redirect('@division');
      }
    }
    parent::preExecute();
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
      $pager->setParameter('division',$this->division);
      return $pager;
  }
  /**
   * This method cannot be executed.
   */
  public function executeNew(sfWebRequest $request)
  {
    $this->getUser()->setFlash('warning', __('This action cannot be executed in this behaviour.'));
    $this->redirect('@division_course');
  }

  public function executeCreateCourse($request)
  {
    $career_subject = CareerSubjectPeer::retrieveByPk($request->getParameter('id'));
    $this->division->createCourse($career_subject->retrieveInstanceForSchoolYear($this->division->getSchoolYear())->getId());
    $this->redirect('@division_course');
  }

  public function executeCopyStudentsFromDivision(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    $this->course = $this->getRoute()->getObject();
    $this->course->copyStudentsFromDivision();
    $this->getUser()->setFlash('notice',__('The students where added successfuly'));
    $this->redirect('@division_course');
  }

  public function executeCopyStudentsToCourses(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    $this->division->copyStudentsToCourses();
    $this->getUser()->setFlash('notice',__('The students where added successfuly'));
    $this->redirect('@division_course');
  }

  public function executeClose(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "division_course");

    $this->forward("shared_course", "close");
  }

  public function executeCalifications(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();

    $this->getUser()->setAttribute("referer_module", "division_course");

    $this->redirect("course_student_mark/index?id=".$this->course->getId());
  }

  public function executeManageCourseDays(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "division_course");

    $this->forward("shared_course", "manageCourseDays");
  }

  public function executeCourseSubjectStudent(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "division_course");

    $this->forward("shared_course", "courseSubjectStudent");
  }

  public function executeCourseTeachers(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "division_course");

    $this->forward("shared_course", "courseTeachers");
  }

  public function executeCourseSubjectStudentsRegularity(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPK($request->getParameter('id'));
    if(empty($this->course))
      $this->redirect('@division_course');
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

  public function executeBackPeriod(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->course->backPeriod();
    $this->getUser()->setFlash('notice',__('The course has back to last period succesfuly.'));
    $this->redirect('@division_course');
  }

  public function executeStudents(sfWebRequest $request)
  {
    $reference_array = array(
      "peer" => "CourseSubjectStudentPeer",
      "fk" => "COURSE_SUBJECT_ID",
      "object_ids" => $this->getRoute()->getObject()->getCourseSubjectIds(),
      "back_to" => "@division_course",
      "title" => "Course student list",
      "back_to_label" => "Return to course list"
    );

    $this->getUser()->setReferenceFor($this, $reference_array, "shared_student");

    $this->redirect('@shared_student');
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->getUser()->setAttribute("referer_module", "division_course");

    $this->forward("shared_course", "show");
  }

  public function executePrintCalification(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();

    if (null === $this->course)
    {
      $this->redirect($this->getModuleName().'/index');
    }

    $this->getUser()->setAttribute('referer_module', 'division_course');

    $this->redirect('course_student_mark/print?id='.$this->course->getId());
  }

  public function executeCourseConfiguration(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->getUser()->setAttribute("referer_module", "division_course");
    $this->redirect("shared_course/courseConfiguration?id=" . $this->course->getId());
  }

  public function executeAttendanceSubject(sfWebRequest $request)
  {
    $course = $this->getRoute()->getObject();
    $career_school_year_id = $course->getCareerSchoolYear()->getId();
    $subjectIds = $course->getCourseSubjectIds();
    $course_subject_id = array_shift($subjectIds);
    $year = $course->getYear();
    $this->getUser()->setAttribute('back_url','division_course');
    $this->redirect("student_attendance/StudentAttendance?year=$year&course_subject_id=$course_subject_id&career_school_year_id=$career_school_year_id&division_id=");
  }

  public function executeCreateAllCourses(sfWebRequest $request)
  {
    $this->division = DivisionPeer::retrieveByPK($request->getParameter('id'));
    $this->division->createAllCourses();
    $this->redirect('@division_course');
  }

  public function executeAttendanceSheetByCourseSubject(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->getUser()->setAttribute("referer_module", "division_course");
    $this->redirect("shared_course/attendanceSheetByCourseSubject?id=" . $this->course->getId());
  }

  public function executeCalificateNonNumericalMark(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->getUser()->setAttribute("referer_module", "division_course");
    $this->redirect("course_student_mark/calificateNonNumericalMark?id=" . $this->course->getId());
  }

  public function executeChangelogMarks(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();

    if (null === $this->course)
    {
      $this->redirect($this->getModuleName().'/index');
    }

    $this->getUser()->setAttribute('referer_module', 'division_course');

    $this->redirect('course_student_mark/changelogMarks?id='.$this->course->getId());
  }
  
  public function executeRevertCalificateNonNumericalMark(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->getUser()->setAttribute("referer_module", "division_course");
    $this->redirect("course_student_mark/revertCalificateNonNumericalMark?id=" . $this->course->getId());
  }
  
  public function executeGenerateRecord(sfWebRequest $request)
  {
       $con =  Propel::getConnection();
       try
       {    $course = $this->getRoute()->getObject();     
            $cs = $course->getCourseSubject();
            $cs->generateRecord();
       }
       catch (Exception $e)
       {
          $con->rollBack();
          $this->getUser()->setFlash('error', 'Ocurrió un error y no se guardaron los cambios.');
          $this->redirect('@division_course');
       }
              
  }
  
  public function executeAssignPhysicalSheet(sfWebRequest $request)
  {
      $course = $this->getRoute()->getObject();     
      $cs = $course->getCourseSubject();
      $this->getUser()->setAttribute("referer_module", "division_course");
      $this->redirect("course_student_mark/assignPhysicalSheet?course_subject_id=" . $cs->getId());
  }
  
}
