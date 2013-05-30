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
 * CourseConfigurationManyForm
 *
 * @author
 */
class CourseSubjectConfigurationManyForm extends sfFormPropel
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $course_subject = $this->getObject();
    $course_type = $course_subject->getCareerSubjectSchoolYear()->getConfiguration()->getCourseType();

    if (!$course_subject->getCourseSubjectConfigurations())
    {

      $c = new Criteria();
      $c->add(CareerSchoolYearPeriodPeer::COURSE_TYPE, $course_type);
      $c->add(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_ID, $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYearId());
      $c->addAscendingOrderByColumn('start_at');
      $periods = CareerSchoolYearPeriodPeer::doSelect($c);
      foreach ($periods as $period)
      {
        $csc = new CourseSubjectConfiguration();
        $csc->setCareerSchoolYearPeriod($period);
        $course_subject->addCourseSubjectConfiguration($csc);
        $csc->save();
      }
    }


    foreach ($course_subject->getCourseSubjectConfigurations() as $course_configuration)
    {
      $name = 'course_configuration_' . $course_configuration->getId();
      $this->setWidget($name, new dcWidgetI18nNumberInput());
      $this->getWidget($name)->setDefault($course_configuration->getMaxAbsence());
      $this->setValidator($name, new dcValidatorI18NDecimal());
      $this->getWidgetSchema()->setLabel($name, $course_configuration->getCareerSchoolYearPeriod());
    }

    $this->getWidgetSchema()->setNameFormat('course_subject[%s]');
  }

  public function getModelName()
  {
    return 'CourseSubject';
  }

  public function doSave($con = null)
  {
    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $course_subject = $this->getObject();
    $values = $this->getValues();
    try
    {
      $con->beginTransaction();

      foreach ($course_subject->getCourseSubjectConfigurations() as $course_subject_configuration)
      {
        $course_subject_configuration->setMaxAbsence($values['course_configuration_' . $course_subject_configuration->getId()]);
        $course_subject_configuration->save($con);
      }

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }
  }

}