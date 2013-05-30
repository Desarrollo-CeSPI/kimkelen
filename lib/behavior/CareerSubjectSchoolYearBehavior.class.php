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
 * @author gramirez
 */
class CareerSubjectSchoolYearBehavior
{
  /**
   * Creates career_subject_school_year if the career has already have an instance of career_school_year of the actual school year.
   *
   * @param CareerSubject $career_subject
   * @param PropelPDO $con
   */
  public function updateCareerSubjectSchoolYear(CareerSubject $career_subject, PropelPDO $con)
  {
    if ($career_subject->isNew())
    {
      $school_year = SchoolYearPeer::retrieveCurrent();      
      $career_school_year = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($career_subject->getCareer(), $school_year);
      if ( !is_null($career_school_year) &&  !$career_school_year->getIsProcessed())
      {
        $career_subject_school_year = new CareerSubjectSchoolYear();
        $career_subject_school_year->setCareerSubjectId($career_subject->getId());
        $career_subject_school_year->setCareerSchoolYearId($career_school_year->getId());        
        $career_subject_school_year->save($con);
      }
    }
  }
}