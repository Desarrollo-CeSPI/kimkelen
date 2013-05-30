<?php

/**
 * Occupation filter form.
 *
 * @package    sistema de alumnos
 * @subpackage filter
 * @author     Your name here
 */
class OccupationFormFilter extends BaseOccupationFormFilter
{
  public function configure()
  {
    $this->widgetSchema['name']->setOption('with_empty', false);
  }
}
