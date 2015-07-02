<?php
/*
 * This file is part of the sfPHPUnit2Plugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Idea taken from bootstrap/unit.php of the lime bootstrap file
 */

$_test_dir = realpath(dirname(__FILE__).'/../..');
$_root_dir = $_test_dir.'/..';

// configuration
require_once $_root_dir.'/config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::hasActive() ? ProjectConfiguration::getActive() : new ProjectConfiguration(realpath($_root_dir));

// lime
require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

//custom base class
require_once dirname(__FILE__).'/../lib/BaseUnitTestCase.class.php';

// autoloader for sfPHPUnit2Plugin libs
$autoload = sfSimpleAutoload::getInstance(sfConfig::get('sf_cache_dir').'/project_autoload.cache');
$autoload->loadConfiguration(sfFinder::type('file')->name('autoload.yml')->in(array(
  sfConfig::get('sf_symfony_lib_dir').'/config/config',
  sfConfig::get('sf_config_dir'),
  $_root_dir . '/plugins/sfPHPUnit2Plugin/lib/config'
)));
$autoload->register();


