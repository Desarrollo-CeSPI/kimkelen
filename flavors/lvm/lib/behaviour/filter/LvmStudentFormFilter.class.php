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
 * Description of LvmStudentFormFilter
 *
 * @author gramirez
 */
class LvmStudentFormFilter extends StudentFormFilter
{

  public function configure() {
    parent::configure();
    $this->setWidget('global_file_number', new sfWidgetFormFilterInput(array('with_empty' => false)));
  }
  public function unsetFields()
  {
    unset(
      $this['person_id'],
      $this['occupation_id'],
      $this['busy_starts_at'],
      $this['busy_ends_at'],
      $this['order_of_merit'],
      $this['folio_number'],
      $this['origin_school'],
      $this['educational_dependency'],
      $this['student_career_subject_allowed_list'],
      $this['blood_group'],
      $this['blood_factor'],
      $this['emergency_information'],
      $this['health_coverage_id'],
      $this['student_tag_list']
    );
  }
  public function getFields()
  {
    return array_merge(parent::getFields(), array('student' => 'Text', 'year' => 'Number', 'division' => 'Number', 'is_matriculated' => 'Boolean', 'global_file_number' => 'Number'));
  }
}