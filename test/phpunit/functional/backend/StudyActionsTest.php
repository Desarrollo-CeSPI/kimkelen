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
require_once dirname(__FILE__).'/../../lib/BaseReferenceFunctionalTestCase.class.php';

class functional_backend_StudyActionsTest extends BaseReferenceFunctionalTestCase
{
  protected function getApplication()
  {
    return 'backend';
  }

  protected function getModule()
  {
    return 'study';
  }
  
  protected function getUrl()
  {
    return '/study';
  }
  
  protected function getAvailableActionChecks()
  {
    return array(
        '.sf_admin_action_new a' => '/Nuevo/',
        '.sf_admin_action_new a' => 2,
    );
  }
  
  protected function getUnvailableActionChecks()
  {
    return array(
        'li.sf_admin_action_disabled' => '/Borrar/',
        'li.sf_admin_action_delete'   => 0,
    );
  }
  
  protected function getFormErrors()
  {
    return array(
      'name' => 'invalid',
    );
  }
  
  protected function getObjectPostData()
  {
    return array('name' => 'prueba');
  }

  public function createObject()
  {
    $obj = new Study();
    $obj->setName('prueba');
    $obj->save();
    
    return $obj;
  }
  
  public function createRelatedObject($obj)
  {
    $r = new Tutor();
    $r->setStudyId($obj->getId());
    $r->save();
  }
}