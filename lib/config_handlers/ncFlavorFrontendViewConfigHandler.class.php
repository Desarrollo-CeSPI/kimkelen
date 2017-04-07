<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php

/**
 * ncFlavorViewConfigHandler
 *
 * @author ncuesta
 */
class ncFlavorFrontendViewConfigHandler extends sfViewConfigHandler
{
  /**
   * Executes this configuration handler.
   *
   * @param array An array of absolute filesystem path to a configuration file
   *
   * @return string Data to be written to a cache file
   *
   * @throws <b>sfConfigurationException</b> If a requested configuration file does not exist or is not readable
   * @throws <b>sfParseException</b> If a requested configuration file is improperly formatted
   * @throws <b>sfInitializationException</b> If a view.yml key check fails
   */
  public function execute($configFiles)
  {
    $configFiles = array_merge($configFiles, $this->currentFlavorConfigFiles());

    return parent::execute($configFiles);
  }

  protected function currentFlavorConfigFiles()
  {
    $current_flavor_path = ncFlavorFlavors::getInstance()->getGlobalPath(false);

    return array(
        $current_flavor_path.'/config/frontend_view.yml'
      );
  }
}
