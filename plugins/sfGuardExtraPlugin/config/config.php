<?php

if (sfConfig::get('app_sf_guard_extra_plugin_routes_register', true))
{
  $this->dispatcher->connect('routing.load_configuration', array('sfGuardExtraRouting', 'listenToRoutingLoadConfigurationEvent'));
}
