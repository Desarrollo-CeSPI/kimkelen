<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Idea taken from bootstrap/functional.php of the lime bootstrap file
 */

require_once dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php';
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

// autoloading does not exist at this point yet, require base classes by hand
$_phpunitPluginDir = dirname(__FILE__).'/../../../plugins/sfPHPUnit2Plugin';
require_once $_phpunitPluginDir.'/lib/test/sfPHPUnitBaseTestCase.class.php';
require_once $_phpunitPluginDir.'/lib/test/sfPHPUnitBaseSeleniumTestCase.class.php';

// remove all cache
sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));
