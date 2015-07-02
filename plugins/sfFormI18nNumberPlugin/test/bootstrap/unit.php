<?php

require_once dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php';

$configuration = new sfProjectConfiguration();
require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';
