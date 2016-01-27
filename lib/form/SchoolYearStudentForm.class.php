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
 * SchoolYearStudent form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class SchoolYearStudentForm extends BaseSchoolYearStudentForm
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');
   
    unset($this['created_at'], $this['school_year_id'], $this['student_id']);
    $this->getWidget('shift_id')->setOption('add_empty',false);

	  $this->setWidget('health_info',  new sfWidgetFormSelect(array(
		  'choices'  => BaseCustomOptionsHolder::getInstance('HealthInfoStatus')->getOptions()
	  )));

  }
}
