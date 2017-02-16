<?php

class tutors_frontendConfiguration extends sfApplicationConfiguration
{
  public function setup()
  {
    $this->setPlugins(array());
    $this->enableAllPluginsExcept(array('sfDoctrinePlugin', 'sfCompat10Plugin', 'pmJSCookMenuPlugin'));
  }

}
