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

class functional_backend_OccupationCategoryActionsTest extends BaseFunctionalTestCase
{
  protected function getApplication()
  {
    return 'backend';
  }

  public function testIndex()
  {
     $this->getBrowser()->
     
      info('Testing index is reachable and has all corresponding actions')->
    
      get('/occupation_category')->

      with('request')->begin()->
        isParameter('module', 'occupation_category')->
        isParameter('action', 'index')->
      end()->

      with('response')->begin()->
        isStatusCode(200)->
        checkElement('#sf_admin_container h1', '/Listado de categorias ocupacionales/')->
        checkElement('.sf_admin_action_new a', '/Nuevo/')->
        checkElement('.sf_admin_action_new a', 2)->
      end()
    ;
  }

  public function testSuccessfullCreate()
  {
    $this->getBrowser()->
        
      info('Testing successfull new & create')->
        
      get('/occupation_category/new')->

      with('request')->begin()->
          isParameter('module', 'occupation_category')->
          isParameter('action', 'new')->
      end()->

      click('Guardar', array('occupation_category' => array('name' => 'prueba')))->

      with('request')->begin()->
        isParameter('module', 'occupation_category')->
        isParameter('action', 'create')->
        isMethod('post')->
      end()->

      with('propel')->check('OccupationCategory', array("name" => "prueba"))->

      with('response')->isRedirected()->followRedirect()->

      with('response')->checkElement('#flash_notice', '/El elemento fue creado satisfactoriamente./')
    ;

  }

  public function testCannotCreateNameInBlank()
  {
    $this->getBrowser()->
       
       info('Testing form submitted is empty')->
        
       get('/occupation_category/new')->
        
       with('request')->begin()->
           isParameter('module', 'occupation_category')->
           isParameter('action', 'new')->
           isMethod('get')->
       end()->

       click('Guardar', array('occupation_category' => array('name' => '')))->
       
       with('request')->begin()->
           isParameter('module', 'occupation_category')->
           isParameter('action', 'create')->
           isMethod('post')->
       end()->

      with('form')->begin()->
           hasErrors(1)->
           isError('name', 'required')->
      end()->
        
      with('response')->
          checkElement('#flash_error', '/El elemento no fue guardado debido a algunos errores/')
    ;
  }
 
  public function testCannotCreateNameNotUnique()
  {
    $cat = $this->createOccupationCategory('prueba');

    $this->getBrowser()->
        
        info('Testing form name value is not unique')->
        
        get('/occupation_category/new')->

        with('request')->begin()->
            isParameter('module', 'occupation_category')->
            isParameter('action', 'new')->
            isMethod('get')->
        end()->        
        
        click('Guardar', array('occupation_category' => array('name' => 'prueba')))->
        
        with('request')->begin()->
            isParameter('module', 'occupation_category')->
            isParameter('action', 'create')->
            isMethod('post')->
        end()->
        
        with('form')->begin()->
            hasErrors(1)->
            isError('name', 'invalid')->
        end()->

        with('response')->
            checkElement('#flash_error', '/El elemento no fue guardado debido a algunos errores/')
     ;
  }
 
  public function testDeleteOcupationCategory()
  {
    $cat = $this->createOccupationCategory('prueba');

    $this->getBrowser()->
        
        info("Testing succesfull delete occupation")->
        
        get("/occupation_category/".$cat->getId()."/delete", $this->addCSRF())->
    
        with('request')->begin()->
          isParameter('module', 'occupation_category')->
          isParameter('action', 'delete')->
          isMethod('get')->
        end()->
            
        with('user')->isFlash('notice','The item was deleted successfully.')->
        
        with('propel')->check('OccupationCategory', array('name'=>'prueba'), false)->
  
        with('response')->isRedirected()->followRedirect()->
        
        with('response')->checkElement('#flash_notice', '/El elemento fue borrado satisfactoriamente./')
    ;

  }
 
  public function testCannotDeleteUsedOccupationCategoryFromIndex()
  {
    $cat = $this->createOccupationCategory();
    $this->createTutor($cat);
    
    $this->getBrowser()->
      info('Testing delete link is not appearing for used occupations')->
        
      get('/occupation_category/index')->
       
      with('response')->begin()->
        isStatusCode('200')->
        checkElement('li.sf_admin_action_disabled', '/Borrar/')->  //cheking that link is rendered disabled
        checkElement('li.sf_admin_action_delete', 0)->             //checking that delete link is rendered 0 times
      end()
    ;
  }
  
  public function testCannotDeleteUsedOccupationCategoryFromUrl()
  {
    $cat = $this->createOccupationCategory('prueba');
    $t = $this->createTutor($cat);
  
    $this->getBrowser()->
        info("Testing succesfull delete occupation")->
        get("/occupation_category/".$cat->getId()."/delete", $this->addCSRF())->
    
        with('request')->begin()->
          isParameter('module', 'occupation_category')->
          isParameter('action', 'delete')->
          isMethod('get')->
        end()->
        
        with('response')->isStatusCode(404)  // cuando se intenta eliminar una ocupacion que es usada,
                                             // tira una sfError404Exception, ¿esto esta bien?
     ;
  }
  
  public function testSuccessfullUpdate()
  {
    $cat = $this->createOccupationCategory();
    
    $this->getBrowser()->
        
      info('Testing successfull update')->
        
      get(sprintf('/occupation_category/%d/edit', $cat->getId()))->
        
      with('request')->begin()->
          isParameter('module', 'occupation_category')->
          isParameter('action', 'edit')->
      end()->

      click('Guardar', array('occupation_category' => array('id' => $cat->getId(), 'name' => 'otraPrueba')))->

      with('request')->begin()->
        isParameter('module', 'occupation_category')->
        isParameter('action', 'update')->
        isMethod('put')->
      end()->

      with('propel')->check('OccupationCategory', array('id' => $cat->getId(), "name" => "otraPrueba"))->

      with('response')->isRedirected()->followRedirect()->

      with('response')->checkElement('#flash_notice', '/El elemento fue actualizado satisfactoriamente./')
    ;
  }
 
  protected function createOccupationCategory($name = 'prueba')
  {
    $cat = new OccupationCategory();
    $cat->setName($name);
    $cat->save();
    
    return $cat;
  }
  
  protected function createTutor($category)
  {
    $t = new Tutor();
    $t->setOccupationCategoryId($category->getId());
    $t->save();
    
    return $t;
  }
}