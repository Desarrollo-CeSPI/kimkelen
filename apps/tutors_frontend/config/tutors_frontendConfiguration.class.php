<?php

class tutors_frontendConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
  }


  public function initialize()
  {
    parent::initialize();

    // Register behavior's hooks


    sfPropelBehavior::registerHooks('person_delete', array(
      ':delete:pre' => array('PersonBehavior', 'deletePerson'),
    ));


  }
}