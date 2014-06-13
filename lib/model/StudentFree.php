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

class StudentFree extends BaseStudentFree
{
  public function __toString()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    if (!is_null($this->getCareerSchoolYearPeriod())) {
    return __('The student is free in the %period%', array('%period%' => $this->getCareerSchoolYearPeriod()));
    }
    else {
      return __('The student is free');
    }
  }

  public function renderChangeLog()
  {
    return ncChangelogRenderer::render($this, 'tooltip', array('credentials' => 'view_changelog'));
  }

}

sfPropelBehavior::add('StudentFree', array('changelog'));