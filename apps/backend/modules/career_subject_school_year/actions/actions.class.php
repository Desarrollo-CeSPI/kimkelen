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

require_once dirname(__FILE__) . '/../lib/career_subject_school_yearGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/career_subject_school_yearGeneratorHelper.class.php';

/**
 * career_subject_school_year actions.
 *
 * @package    sistema de alumnos
 * @subpackage career_subject_school_year
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class career_subject_school_yearActions extends autoCareer_subject_school_yearActions
{

  /**
   * Redefines preExecute because this action CANT BE RISED WITHOUT A REFERENCE
   *
   */
  public function preExecute()
  {
    if (!$this->getUser()->getReferenceFor('career_school_year'))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera del año lectivo para poder administrar sus materias.');
      $this->redirect('@career_school_year');
    }
    $this->career_school_year = CareerSchoolYearPeer::retrieveByPK($this->getUser()->getReferenceFor('career_school_year'));

    if (is_null($this->career_school_year))
    {
      $this->getUser()->setFlash('warning', 'Debe seleccionar una carrera del año lectivo para poder administrar sus materias.');
      $this->redirect('@career_school_year');
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
    $pager->setParameter('career_school_year', $this->career_school_year);
    return $pager;

  }

  /**
   * This action render ther SubjectConfiguration Form
   */
  public function executeConfiguration(sfWebRequest $request)
  {
    $this->career_subject_school_year = $this->getRoute()->getObject();
    $subject_configuration = $this->career_subject_school_year->getSubjectConfigurationOrCreate();

    $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCareerSubjectSchoolYearConfigurationForm();
    $this->form = new $form_name($subject_configuration);

  }

  /*
   *  This method validates the form, and if it is correct, it is saved.
   *
   */

  public function executeUpdateConfiguration(sfWebRequest $request)
  {
    $this->career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByPK($request->getParameter('id'));
    if (null === $this->career_school_year)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una carrera para editar su configuracion');
      $this->redirect('@career_school_year');
    }

    $subject_configuration = $this->career_subject_school_year->getSubjectConfigurationOrCreate();
    $this->career_subject_school_year->setSubjectConfiguration($subject_configuration);

    $parameters = $request->getPostParameters();
    $parameter = $parameters["subject_configuration"];

    if ($this->career_subject_school_year->hasChoices())
    {
      $course_subjects = array();
      foreach ($this->career_subject_school_year->getChoices() as $option)
      {
        $course_subjects = array_merge($course_subjects, CourseSubjectPeer::retrieveByCareerSubjectSchoolYear($option->getChoiceCareerSubjectSchoolYearId()));
      }
    }
    else
    {
      $course_subjects = CourseSubjectPeer::retrieveByCareerSubjectSchoolYear($this->career_subject_school_year->getId());
    }

    //actualización de las notas para los cursos
    $this->updateCourseMarksConfiguration($parameter, $subject_configuration, $course_subjects);
    //actualización del tipo de curso para los cursos
    $this->updateCourseTypeConfiguration($parameter, $subject_configuration, $course_subjects);
    //actualización del tipo de asistencia para los cursos y las asistencias de los alumnos
    $this->updateStudentAssistanceConfiguration($parameter, $subject_configuration, $course_subjects);

    $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCareerSubjectSchoolYearConfigurationForm();
    $this->form = new $form_name($subject_configuration);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $notice = $this->getProcessFormNotice($this->form->getObject()->isNew());

      $subject_configuration = $this->form->save();

      $this->getUser()->setFlash('notice', $notice);
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
    $this->setTemplate('configuration');

  }

  private function updateCourseMarksConfiguration($parameter, $subject_configuration, $course_subjects)
  {
    if (!isset($parameter["course_marks"]))
    {

      $parameter["course_marks"] = SchoolBehaviourFactory::getInstance()->getMarksForCourseType($parameter["course_type"]);
    }

    if (isset($parameter["course_marks"]))
    {
      $course_marks = (int) $parameter["course_marks"];
      
      if ($course_marks != $subject_configuration->getCourseMarks())
      {
        foreach ($course_subjects as $course_subject)
        {
          $course_subject->updateCourseMarks($course_marks);
        }
      }
    }

  }

  private function updateCourseTypeConfiguration($parameter, $subject_configuration, $course_subjects)
  {
    if (isset($parameter["course_type"]) && $parameter["course_type"] != $subject_configuration->getCourseType())
    {
      foreach ($course_subjects as $course_subject)
      {
        //$subject_configuration->updateCourseType($parameter["course_type"]);
        $course_subject->deleteCourseSubjectConfiguration();
      }
    }

  }

  private function updateStudentAssistanceConfiguration($parameter, $subject_configuration, $course_subjects)
  {
    $previous_course_type = $subject_configuration->getCourseType();
    if (isset($parameter["attendance_type"]) && $parameter["attendance_type"] != $previous_course_type)
    {
      foreach ($course_subjects as $course_subject)
      {
        // si estaba puesto asistencia por materia, hay que borrar lo relacionado a eso
        if (SchoolBehaviourFactory::getInstance()->getAttendanceSubject() == $previous_course_type)
        {
          $course_subject->deleteStudentAttendances();
        }
      }
    }

  }

  /*   * ***************
   * batch actions *
   * *************** */

  public function executeBatchConfiguration(sfWebRequest $request, $objects)
  {
    $ids = array_map(create_function("\$o", "return \$o->getId();"), $objects);
    $this->getUser()->setAttribute("multiple_configuration_career_subject_school_year_ids", implode(",", $ids));

    $this->redirect("career_subject_school_year/multipleConfiguration");

  }

  public function executeMultipleConfiguration(sfWebRequest $request)
  {
    $this->title = "Subject configuration";
    $this->help = "Previous saved configurations will be overriden";
    $this->url = "career_subject_school_year/multipleConfiguration";

    $this->setTemplate("commonBatch");

    $ids = $this->getUser()->getAttribute("multiple_configuration_career_subject_school_year_ids");
    $ids = explode(",", $ids);
    $form_name = SchoolBehaviourFactory::getInstance()->getFormFactory()->getMultipleSubjectConfigurationForm();
    $this->form = new $form_name;
    $this->form->setCareerSubjectSchoolYearsIds($ids);

    if ($request->isMethod("post"))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $this->getUser()->setFlash("notice", "All subjects have been configured.");
        $this->getUser()->getAttributeHolder()->remove("multiple_configuration_career_subject_school_year_ids");

        $this->redirect("@career_subject_school_year");
      }
    }

  }

}
