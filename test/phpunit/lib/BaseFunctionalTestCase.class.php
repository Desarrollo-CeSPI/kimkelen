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
 * Empty class for extending plugin test cases
 * 
 */
class BaseFunctionalTestCase extends sfPHPUnitBaseFunctionalTestCase
{
  /*
   * fix for using processIsolation="true" in phpunit configuration
   * to keep memory usage low.
   * override phpunit run in order to set preserve_global_state = false.
   * Otherwise, an "RuntimeException: PHP Notice: Constant SYMFONY_VERSION already defined"
   * exception is thrown.
   *
   * @see: http://css.dzone.com/news/process-isolation-phpunit-0
   */
  public function run(PHPUnit_Framework_TestResult $result = NULL)
  {
    $this->setPreserveGlobalState(false);

    return parent::run($result);
  }

  function getBrowser()
  {
    $browser = parent::getBrowser();
    $browser->setTester("propel", "sfTesterPropel");

    return $browser;		
  }

  public function _start()
  {
    parent::_start();

    ncPropelChangeLogBehavior::disable();

    new sfDatabaseManager($this->getApplicationConfiguration());

    $this->loadData();
    $this->login();
  }

  public function _end()
  {
    $this->logout();
  }
    
  protected function loadData($fixtures = null)
  {
    if(empty($fixtures))
    {
      $fixtures = sfConfig::get('sf_test_dir') . '/phpunit/fixtures/testing.yml';
    }
    
    $data = new sfPropelData();
    $data->loadData($fixtures);
  }

  protected function login($username = 'admin', $password = 'admin')
  {
    $post = array(
        'username' => $username,
        'password' => $password,
    );

    $this->addCSRF($post, 'sfGuardFormSignin');

    $this->getBrowser()->
      post('/login', array('signin' => $post))->
      with('response')->begin()->
        isRedirected()->
        followRedirect()->
      end()
    ;
  }

  public function logout()
  {
    $this->getBrowser()->get('/logout');    
  }

  protected function addCSRF(&$post = array(), $class = 'sfForm')
  {
    $form  = new $class();
    $field = $form->getCSRFFieldName();
    $token = $form->getCSRFToken();

    $post[$field] = $token;

    return $post;
  }

  protected function getCourse($name)
  {
    $criteria = new Criteria();
    $criteria->add(CoursePeer::NAME, $name);
    
    return CoursePeer::doSelectOne($criteria);
  }
  
  protected function getAbsencesType($name, $method)
  {
    $criteria = new Criteria();
    $criteria
      ->add(AbsenceTypePeer::NAME, $name)
      ->add(AbsenceTypePeer::METHOD, $method)
    ;
    
    return AbsenceTypePeer::doSelectOne($criteria);
  }

  protected function getSubjectAbsencesType($name)
  {
    return $this->getAbsencesType($name, 1);
  }

  protected function getDayAbsencesType($name)
  {
    return $this->getAbsencesType($name, 0);
  }
}