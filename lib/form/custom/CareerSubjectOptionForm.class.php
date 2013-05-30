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
 * CareerSubjectOptionForm is a speciallized form for career subjects
 * that are option
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class CareerSubjectOptionForm extends CareerSubjectForm {

  public function configure() {
    parent::configure();
    unset(
          $this['has_options'],
          $this['credit_hours'],
          $this['has_correlative_previous_year'],
          $this['type']
    );
  }
  
  public function updateObject($values = null) {
    parent::updateObject($values);
    $this->getObject()->setIsOption(true);
  }

}
?>