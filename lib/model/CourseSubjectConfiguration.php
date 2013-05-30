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

class CourseSubjectConfiguration extends BaseCourseSubjectConfiguration
{
  public function getPeriod(){
    return $this->getCareerSchoolYearPeriod();
  }

  /**
   *Este metodo recupera el  periodo padre de un curso bimestral  y devuelve  tru si es el primero del cuatrimester
   *Siempre va a exisiter un solo periodo padre distinto al que recupero
   */
  public function parentIsFirst()
  {
    $parent_period = CareerSchoolYearPeriodPeer::retrieveByPK($this->getPeriod()->getCareerSchoolYearPeriodId());
    
    $c = new Criteria();
    $c->add(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_ID,$parent_period->getCareerSchoolYearId());
    $c->add(CareerSchoolYearPeriodPeer::COURSE_TYPE,$parent_period->getCourseType());
    $c->add(CareerSchoolYearPeriodPeer::ID,$parent_period->getId(),Criteria::NOT_EQUAL);
    $compare_perdiod = CareerSchoolYearPeriodPeer::doSelectOne($c);


    return $parent_period->getStartAt() < $compare_perdiod->getStartAt();


  }
}