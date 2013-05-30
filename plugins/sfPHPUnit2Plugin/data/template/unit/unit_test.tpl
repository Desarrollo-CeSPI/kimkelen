<?php
require_once dirname(__FILE__).'{dir}/bootstrap/unit.php';

class unit_{test_class}Test extends BaseUnitTestCase
{
  public function testDefault()
  {
    $t = $this->getTest();

    // lime-like assertions
    //$t->diag('hello world');
    //$t->ok(true, 'test something');
		
    // native assertions
    //$this->assertTrue(true, 'test something')
  }
}