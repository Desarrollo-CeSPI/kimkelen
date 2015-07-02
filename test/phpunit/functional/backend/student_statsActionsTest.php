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
require_once dirname(__FILE__).'/../../bootstrap/functional.php';

class functional_backend_student_statsActionsTest extends BaseFunctionalTestCase
{
  protected function getApplication()
  {
    return 'backend';
  }

   public function testEdit()
  {
    $browser = $this->getBrowser();

    $browser->
      get('/student_stats')->

      with('request')->begin()->
        isParameter('module', 'student_stats')->
        isParameter('action', 'index')->
      end()
    ;
  }

   public function testShow()
  {
    $browser = $this->getBrowser();

    $browser->
      get('/student_stats')->

      with('request')->begin()->
        isParameter('module', 'student_stats')->
        isParameter('action', 'index')->
      end()
    ;
  }
}