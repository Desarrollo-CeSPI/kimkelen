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

class AgropecuariaSchoolBehaviour extends BaseSchoolBehaviour
{


  protected $school_name  ="Escuela de Educación Técnico Profesional de Nivel Medio en Producción Agropecuaria y Agroalimentaria - Facultad de Ciencias Veterinarias de la UBA";
  protected $HOUR_FOR_SCHEMA = array(7 => "07", 8 => "08", 9 => "09", 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18);
  protected $MINUTES_FOR_SCHEMA = array(0 => "00", "05" => "05", 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40, 45 => 45, 50 => 50, 55 => 55);

  const BLOCKS_PER_COURSE_SUBJECT_DAY = 2;

  public function getDefaultCityId()
  {
    return City::BUENOS_AIRES;

  }

  public function getDefaultStateId()
  {
    return State::CIUDAD_AUTONOMA;

  }

  public function getBlocksPerCourseSubjectDay()
  {
    return self::BLOCKS_PER_COURSE_SUBJECT_DAY;

  }

  public function showReportCardRepproveds()
  {
    return true;

  }

  public function getDivisionCourseType()
  {
    return CourseType::QUATERLY;

  }

  public function canRelatedToDivision($division = null, $is_current_school_year = null)
  {
    return is_null($division) && $is_current_school_year;

  }
}