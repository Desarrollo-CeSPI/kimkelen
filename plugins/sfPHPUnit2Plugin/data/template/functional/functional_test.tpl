<?php
require_once dirname(__FILE__).'/../../bootstrap/functional.php';

class functional_{controller_class}ActionsTest extends BaseFunctionalTestCase
{
  protected function getApplication()
  {
    return '{application}';
  }

  public function testDefault()
  {
    $browser = $this->getBrowser();

    $browser->
      get('/{controller_name}/index')->

      with('request')->begin()->
        isParameter('module', '{controller_name}')->
        isParameter('action', 'index')->
      end()->

      with('response')->begin()->
        isStatusCode(200)->
        checkElement('body', '!/This is a temporary page/')->
      end()
    ;
  }
}