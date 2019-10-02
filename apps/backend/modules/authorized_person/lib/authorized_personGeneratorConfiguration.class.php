<?php

/**
 * authorized_person module configuration.
 *
 * @package    symfony
 * @subpackage authorized_person
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 12474 2008-10-31 10:41:27Z fabien $
 */
class authorized_personGeneratorConfiguration extends BaseAuthorized_personGeneratorConfiguration
{
    
  public function getForm($object = null) 
  {
    $form = SchoolBehaviourFactory::getInstance()->getFormFactory()->getAuthorizedPersonForm();
    return new $form($object);
  }
}
