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

class functional_backend_OccupationActionsTest extends BaseFunctionalTestCase 
{

  protected function getApplication()
  {
    return 'backend';
  }

  public function testIndex()
  {
     $this->getBrowser()->
      get('/occupation')->

      with('request')->begin()->
        isParameter('module', 'occupation')->
        isParameter('action', 'index')->
      end()->

      with('response')->begin()->
        isStatusCode(200)->
        checkElement('#sf_admin_container h1', '/Listado de ocupaciones/')->
        checkElement('.sf_admin_action_new a', '/Nueva/')->
        checkElement('.sf_admin_action_new a', 2)->
      end()
    ;
  }

  public function testSuccessfullCreate()
  {
    $this->getBrowser()->
      get('/occupation/new')->

      with('request')->begin()->
          isParameter('module', 'occupation')->
          isParameter('action', 'new')->
      end()->

      click('Guardar ocupación', array('occupation' => array('name' => 'new')))->

      with('request')->begin()->
        isParameter('module', 'occupation')->
        isParameter('action', 'create')->
        isMethod('post')->
      end()->

      with('propel')->check('Occupation', array("name" => "new"))->

      with('response')->isRedirected()->followRedirect()->

      with('response')->checkElement('#flash_notice', '/La ocupación fue creada correctamente/')
    ;
  }

  public function testCannotCreateNameInBlank()
  {
    $this->getBrowser()->
      get('/occupation/new')->

      with('request')->begin()->
          isParameter('module', 'occupation')->
          isParameter('action', 'new')->
          isMethod('get')->
      end()->         

      click('Guardar ocupación', array('occupation' => array('name' => '')))->

      with('request')->begin()->
          isParameter('module', 'occupation')->
          isParameter('action', 'create')->
          isMethod('post')->
      end()->

      with('form')->begin()->
          hasErrors(1)->
          isError('name', 'required')->
      end()->

      with('response')->
          checkElement('#flash_error', '/La ocupación no fue guardada debido a algunos errores/')
    ;
  }

  public function testCannotCreateNameNotUnique()
  {
    $this->createOccupation('test');

    $this->getBrowser()->
        get('/occupation/new')->

        with('request')->begin()->
            isParameter('module', 'occupation')->
            isParameter('action', 'new')->
            isMethod('get')->
        end()->
        
        click('Guardar ocupación', array('occupation' => array('name' => 'test')))->

        with('request')->begin()->
            isParameter('module', 'occupation')->
            isParameter('action', 'create')->
            isMethod('post')->
        end()->

        with('form')->begin()->
            hasErrors(1)->
            isError('name', 'invalid')->
        end()->

        with('response')->
            checkElement('#flash_error', '/La ocupación no fue guardada debido a algunos errores/')
     ;
  }

  public function testDeleteOcupation()
  {
    $occ = $this->createOccupation('test');
    $this->getBrowser()->
        get("/ocupaciones/".$occ->getId()."/delete", $this->addCSRF())->
    
        with('request')->begin()->
          isParameter('module', 'occupation')->
          isParameter('action', 'delete')->
          isMethod('get')->
        end()->
            
        with('user')->isFlash('notice','La ocupación fue eliminada correctamente.')->
        
        with('propel')->check('Occupation', array('name'=>'prueba'), false)->
  
        with('response')->isRedirected()->followRedirect()->

        with('response')->checkElement('#flash_notice', '/La ocupación fue eliminada correctamente/')
    ;
  }

  public function testCannotDeleteUsedOccupationFromIndex()
  {
    $this->createUsedOccupation('test');

    $this->getBrowser()->
      get('/occupation/index')->
       
      with('response')->begin()->
        isStatusCode('200')->
        checkElement('li.sf_admin_action_disabled', '/Borrar/')->  //cheking that link is rendered disabled
        checkElement('li.sf_admin_action_delete', 0)->             //checking that delete link is rendered 0 times
      end()
    ;
  }

  public function testCannotDeleteUsedOccupationFromUrl()
  {
    $occ = $this->createUsedOccupation('used');

    $this->getBrowser()->
        get("/ocupaciones/".$occ->getId()."/delete", $this->addCSRF())->
    
        with('request')->begin()->
          isParameter('module', 'occupation')->
          isParameter('action', 'delete')->
          isMethod('get')->
        end()->
        
        with('response')->isStatusCode(404)  // cuando se intenta eliminar una ocupacion que es usada,
                                             // tira una sfError404Exception, ¿esto esta bien?
     ;
  }

  public function testSuccessfullUpdate()
  {
    $occ = $this->createOccupation('new');
    
    $this->getBrowser()->
      get(sprintf('/ocupaciones/%d/edit', $occ->getId()))->
        
      with('request')->begin()->
          isParameter('module', 'occupation')->
          isParameter('action', 'edit')->
      end()->

      click('Guardar ocupación', array('occupation' => array('id' => $occ->getId(), 'name' => 'notNew')))->

      with('request')->begin()->
        isParameter('module', 'occupation')->
        isParameter('action', 'update')->
        isMethod('put')->
      end()->

      with('propel')->check('Occupation', array('id' => $occ->getId(), "name" => "notNew"))->

      with('response')->isRedirected()->followRedirect()->

      with('response')->checkElement('#flash_notice', '/La ocupación fue actualizada correctamente./')
    ;
    
  }

  public function createOccupation($name)
  {
    $occ = new Occupation();
    $occ->setName($name);
    $occ->save();

    return $occ;
  }

  public function createUsedOccupation($name)
  {
    $occ = $this->createOccupation($name);

    $t = new Tutor();
    $t->setOccupationId($occ->getId());
    $t->save();

    return $occ;
  }
}