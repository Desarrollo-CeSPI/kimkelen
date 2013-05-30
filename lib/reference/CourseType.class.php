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

class CourseType extends BaseCustomOptionsHolder
{

  const
    TRIMESTER = 1,
    QUATERLY = 2,
    BIMESTER = 3,
    QUATERLY_OF_A_TERM = 4;

  protected
  $_marks = array(
    self::TRIMESTER => 3,
    self::QUATERLY => 3,
    self::BIMESTER => 2,
    self::QUATERLY_OF_A_TERM => 1
  );

  protected
  $_options = array(
    self::TRIMESTER => 'Anual con Régimen Trimestral',
    self::QUATERLY => 'Anual con Régimen Cuatrimestral',
    self::BIMESTER => 'Cuatrimestral con Régimen Bimestral',
    self::QUATERLY_OF_A_TERM => 'Cuatrimestral con Régimen de un termino'
  );

  public static function getOption($key)
  {
    $array = self::getOptionsInArray();
    return $array[$key];
  }

  public static function getOptionsInArray()
  {
    return array(
      self::TRIMESTER => 'Anual con Régimen Trimestral',
      self::QUATERLY => 'Anual con Régimen Cuatrimestral',
      self::BIMESTER => 'Cuatrimestral con Régimen Bimestral',
      self::QUATERLY_OF_A_TERM => 'Cuatrimestral con Régimen de un termino'
    );
  }

  protected
  $_evaluation_methods = array(
    self::TRIMESTER => EvaluationMethod::NORMAL,
    self::QUATERLY => EvaluationMethod::FINAL_PROM
  );

  public function getDefaultValue()
  {
    return self::TRIMESTER;
  }

  public function getMarksFor($key, $default_value = null)
  {
    return $this->_marks[$key];
  }

  public function getEvaluationMethodFor($key, $default_value = null)
  {
    return $this->_evaluation_methods[$key];
  }
}