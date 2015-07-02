<?php

/**
 * SchoolYear filter form.
 *
 * @package    conservatorio
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class SchoolYearFormFilter extends BaseSchoolYearFormFilter
{
  static $_states = array(
      ''    => 'Indistinto',
      true  => 'Sí',
      false => 'No'
    );

  public function configure()
  {
    unset($this['year']);
    $this->getWidget('state')->setOption('choices', $this->getStates());

    $this->setValidator('state', new sfValidatorChoice(array(
        'choices'  => array_keys($this->getStates()),
        'required' => false
      )));
  }

  public function getStates()
  {
    return self::$_states;
  }
}
