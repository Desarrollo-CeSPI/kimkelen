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

class SubjectConfiguration extends BaseSubjectConfiguration
{
  public function __toString()
  {
      return'';
  }

  public function hasAttendanceForSubject()
  {
    return $this->getAttendanceType() == SchoolBehaviourFactory::getInstance()->getAttendanceSubject();
  }

  public function updateCourseType($course_type)
  {
    return $this->setCourseType($course_type)->save();
  }

  public function updateAttendanceType($attendance_type)
  {
    return $this->setAttendanceType($attendance_type)->save();
  }

  public function getAttendanceTypeString()
  {
    if($this->getAttendanceType())
    {
      $choices = SchoolBehaviourFactory::getInstance()->getAttendanceTypeChoices();
      return $choices[$this->getAttendanceType()];
    }
    return '';
  }

  public function getCourseTypeString()
  {
    if($this->getCourseType())
    {
      $choices = SchoolBehaviourFactory::getInstance()->getCourseTypeChoices();
      return $choices[$this->getCourseType()];
    }
    return '';
  }

  public function getCareerSchoolYear()
  {
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::SUBJECT_CONFIGURATION_ID, $this->getId());

    return CareerSchoolYearPeer::doSelectOne($c);
  }


  public function getCareerYearConfigurationsOrCreate()
  {
    $career_year_configurations = $this->getCareerYearConfigurations();

    if (count($career_year_configurations) == 0 && $this->getCareerSchoolYear())
    {
      foreach ( $this->getCareerSchoolYear()->getCareer()->getYearsRange() as $i => $year)
      {
        $career_year_configuration = new CareerYearConfiguration();
        $career_year_configuration->setSubjectConfiguration($this);
        $career_year_configuration->setYear($year);
        $career_year_configuration->save();

        $career_year_configurations[] = $career_year_configuration;
      }
    }

    return $career_year_configurations;
  }

  public function getCareerYearConfiguration($year)
  {
    $c = new Criteria();
    $c->add(CareerYearConfigurationPeer::YEAR, $year);
    $c->add(CareerYearConfigurationPeer::SUBJECT_CONFIGURATION_ID, $this->getId());

    return CareerYearConfigurationPeer::doSelectOne($c);
  }

  public function getCourseTypeForYear($year)
  {
    return ($career_year_configuration = $this->getCareerYearConfiguration($year)) ? $career_year_configuration->getCourseType(): null ;
  }


  public function getIsAbsenceForPeriodInYear($year)
  {
    return ($career_year_configuration = $this->getCareerYearConfiguration($year))? $career_year_configuration->getHasMaxAbsenceByPeriod(): null ;
  }

  public function getMaxAbsenceInYear($year)
  {
    return ($career_year_configuration = $this->getCareerYearConfiguration($year))? $career_year_configuration->getMaxAbsences(): null ;
  }

  public function isNumericalMark()
  {
    return ($this->getNumericalMark()) ? true : false;
  }
  
}