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
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of csWidgetFormStudentManyFilterCriteriaByDivision
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class csWidgetFormStudentManyFilterCriteriaByDivision extends csWidgetFormStudentManyFilterCriteria{
    //put your code here
  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
    $this->addOption('title','By division');
    $this->addOption('url',url_for('csWidgetFormStudentMany/filterByDivision'));
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return "By division";
  }
}
?>