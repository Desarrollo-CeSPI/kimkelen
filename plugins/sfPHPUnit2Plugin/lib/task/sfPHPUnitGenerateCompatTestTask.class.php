<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates bootstrap files for PHPUnit testing within older symfony versions.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPHPUnitGenerateCompatTestTask extends sfPHPUnitGenerateBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace        = 'phpunit';
    $this->name             = 'generate-compat';
    $this->briefDescription = 'Generates bootstrap files for older symfony versions';
    $this->detailedDescription = <<<EOF
The [phpunit:generate-compat|INFO] generates bootstrap files for older symfony versions.

Call it with:

  [php symfony phpunit:generate-compat|INFO]
EOF;
  }

   /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // create standard bootstrap files first
    $this->createBootstrap();

    // replace unit bootstrap file with the compat one
    // the functional bootstrap file needs no overwritting
    $template = $this->getTemplate('unit/bootstrap_compat.tpl');

    $rendered = $this->renderTemplate($template, array());

    $file = 'bootstrap/unit.php';
    $this->saveFile($rendered, $file, array('overwrite' => true));

    $this->logSection('file+', $file);
  }
}
