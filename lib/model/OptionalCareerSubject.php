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

class OptionalCareerSubject extends BaseOptionalCareerSubject
{
  public function __toString()
  {
    try
    {
      $career_subject_school_year = $this->getCareerSubjectSchoolYearRelatedByChoiceCareerSubjectSchoolYearId();
    }
    catch (Exception $e)
    {
      
    }
    return $career_subject_school_year->getCareerSubject()->__toString();
  }

  public function createCorrelatives()
  {
    foreach ($this->getCareerSubjectRelatedByCareerSubjectId()->getCorrelatives() as $correlative )
    {
      $new_correlative = new Correlative();
      $new_correlative->setCareerSubjectId($this->getOptionalCareerSubjectId());
      $new_correlative->setCorrelativeCareerSubjectId($correlative->getCorrelativeCareerSubjectId());
      $new_correlative->save();
    }
  }

  public function getOption()
  {
    throw new LogicException('Borrá la referencia al método getOption() de OptionalCareerSubject!');
  }
  public function getCareerSubjectSchoolYear()
  {
      return CareerSubjectSchoolYearPeer::retrieveByPK($this->getCareerSubjectSchoolYearId());
  }
}