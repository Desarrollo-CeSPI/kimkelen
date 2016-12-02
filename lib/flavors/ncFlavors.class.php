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
 * ncFlavorFlavors
 *
 * @author ncuesta
 */
class ncFlavorFlavors
{
  static protected $_instance;

  /**
   * Return the instance of ncFlavorFlavors, and optionally force a re-check
   * of configuration files.
   * 
   * @param  Boolean $force_check_config True if configuration must be checked
   * 
   * @return ncFlavorFlavors The object instance
   */
  static public function getInstance($force_check_config = false)
  {
    if (is_null(self::$_instance) || $force_check_config)
    {
      self::checkConfig();

      $_instance = new self();
    }

    return $_instance;
  }

  /**
   * Check configuration files.
   */
  static public function checkConfig()
  {
	if (sfContext::hasInstance()){
	    include(sfContext::getInstance()->getConfigCache()->checkConfig(sfConfig::get('sf_config_dir').'/nc_flavor.yml'));
	}
  }
  
  /**
   * Return an array holding the names of every ncFlavor flavor that is
   * currently available (installed).
   *
   * @param  Boolean $include_keys Whether to also use the flavor names as keys
   *                               of the resulting array, or just as values
   *
   * @return Array   The array of available (installed) flavors
   */
  public function getAll($include_keys = false)
  {
    $dirnames = sfFinder::type('dir')->maxdepth(0)->ignore_version_control()->in(self::getPath());

    if ($include_keys !== false)
    {
      $flavors = array();
      foreach ($dirnames as $dirname)
      {
        $flavors[basename($dirname)] = basename($dirname);
      }
    }
    else
    {
      $flavors = array_map('basename', $dirnames);
    }

    natsort($flavors);

    return $flavors;
  }

  /**
   * Return the name of the currently selected ncFlavor flavor.
   * 
   * @return string The name of the currently selected ncFlavor flavor
   */
  public function current()
  {
    return sfConfig::get('nc_flavor_flavors_current', 'demo');
  }

  /**
   * Set the current flavor to $current_flavor.
   *
   * @throws sfConfigurationException If unable to write to configuration file
   *
   * @param  string $current_flavor The current flavor to be set
   *
   * @return string The current flavor
   */
  public function setCurrent($current_flavor = 'nc_flavor', $throw_exception = false)
  {
    $config = sfYaml::load(self::getConfigFilePath());
    if (empty($config) || !isset($config['nc_flavor']) || !isset($config['nc_flavor']['flavors']))
    {
      $config = array('nc_flavor' => array('flavors' => array('current' => $current_flavor, 'root_dir' => 'flavors')));
    }
    else
    {
      $config['nc_flavor']['flavors']['current'] = $current_flavor;
    }

    self::dump($config, $throw_exception);

    return $current_flavor;
  }

  /**
   * Dump $config to the configuration file.
   * If $throw_exception is true, an exception will be thrown if configuration
   * file is not writable.
   * This method already re-checks config.
   *
   * @throws sfConfigurationException If a write error occurs and $throw_exception
   *                                  is true
   *
   * @param Array   $config         The array of configuration values
   * @param Boolean $throw_exeption True if an exception must be throw on write error
   */
  static public function dump($config, $throw_exception)
  {
    $yaml = sfYaml::dump($config);

    if (is_writable(self::getConfigFilePath()))
    {
      file_put_contents(self::getConfigFilePath(), $yaml);

      // Force config re-check
      self::checkConfig();
    }
    elseif ($throw_exception)
    {
      throw new sfConfigurationException('Unable to write to nc_flavor flavors configuration file: '.self::getConfigFilePath());
    }
  }

  /**
   * Return the path to the flavors' root dir.
   *
   * @param  Boolean $relative Whether the path should be relative (false by default)
   *
   * @return string  The route to the flavors root dir
   */
  static public function getPath($relative = false)
  {
    $path  = ($relative ? '' : sfConfig::get('sf_root_dir').'/');
    $path .= sfConfig::get('nc_flavor_flavors_root_dir', 'flavors');
    
    return $path;
  }

  static public function getModulePath($module_name = null, $relative = false)
  {
    $path = self::getPath($relative).'/'.self::getInstance()->current().'/modules';
    if (!is_null($module_name))
    {
      $path .= '/'.$module_name;
    }

    return $path;
  }

  static public function getGlobalPath($relative = false)
  {
    return self::getPath($relative).'/'.self::getInstance()->current();
  }

  /**
   * Return the path to the config file (nc_flavor.yml).
   * 
   * @return string The path
   */
  static protected function getConfigFilePath()
  {
    return sfConfig::get('sf_config_dir').'/nc_flavor.yml';
  }

  /**
   * Return the path to the flavor lib folder.
   *
   * @return string The path
   */
  static public function getLibPath()
  {
    return self::getGlobalPath().'/lib';
  }
}
