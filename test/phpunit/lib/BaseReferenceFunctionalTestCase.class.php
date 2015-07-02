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

abstract class BaseReferenceFunctionalTestCase extends BaseFunctionalTestCase
{
  protected function getApplication()
  {
    return 'backend';
  }

  protected abstract function getModule();
  
  protected abstract function getUrl();
  
  protected abstract function getObjectPostData();
  
  protected abstract function createObject();
  
  protected abstract function createRelatedObject($obj);
  
  protected function getAvailableActionChecks()
  {
    return array();
  }
  
  protected function getUnvailableActionChecks()
  {
    return array();
  }
  
  protected function getFormErrors()
  {
    return array();
  }
  
  public function testIndex()
  {
    $obj = $this->createObject();
    
    $this->getBrowser()->
      get($this->getUrl())->

      with('request')->begin()->
        isParameter('module', $this->getModule())->
        isParameter('action', 'index')->
      end()->
      
      with('response')->isStatusCode(200);
      
      $this->checkAvailableActions();
  }
  
  public function testCannotCreateNotUnique()
  {
    $obj = $this->createObject();

    $this->getBrowser()->
      get($this->getUrl().'/new')->

      with('request')->begin()->
          isParameter('module', $this->getModule())->
          isParameter('action', 'new')->
          isMethod('get')->
      end()-> 

      click('Guardar', array($this->getModule() => $this->getObjectPostData()))->

      with('request')->begin()->
          isParameter('module', $this->getModule())->
          isParameter('action', 'create')->
          isMethod('post')->
      end()->

      with('form')->hasErrors(true);

      $this->checkFormErrors();
  }

  public function testCannotDeleteRelatedFromIndex()
  {
    $obj = $this->createObject();
    $rel = $this->createRelatedObject($obj);

    $this->getBrowser()->
      get('/'.$this->getModule().'/index')->
       
      with('response')->isStatusCode('200');
    
    $this->checkUnavailableActions();
  }
  
  public function testCannotDeleteRelatedFromUrl()
  {
    $obj = $this->createObject();
    $rel = $this->createRelatedObject($obj);
    
    $this->getBrowser()->
        info("Testing cannot delete used Study")->
        
        get($this->getUrl()."/".$obj->getId()."/delete", $this->addCSRF())->
    
        with('request')->begin()->
          isParameter('module', $this->getModule())->
          isParameter('action', 'delete')->
          isMethod('get')->
        end()->
        
        with('response')->isStatusCode(404)  // cuando se intenta eliminar una ocupacion que es usada,
                                             // tira una sfError404Exception, ¿esto esta bien?
     ;
  }
  
  protected function checkFormErrors()
  {
    $browser = $this->getBrowser();
    
    foreach ($this->getFormErrors() as $field => $error)
    {
      $browser->with('form')->isError($field, $error);
    }
  }

  protected function checkAvailableActions()
  {
    $this->checkElements($this->getAvailableActionChecks());
  }
  
  protected function checkUnavailableActions()
  {
    $this->checkElements($this->getUnvailableActionChecks());
  }
  
  protected function checkElements($elements)
  {
    $browser = $this->getBrowser();
    
    foreach ($elements as $selector => $value)
    {
      $browser->with('response')->checkElement($selector, $value);
    }
  }
}