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
 * BaseCustomOptionsHolder
 * Base class for any custom options holder class.
 * This class will implement the basic functionallity to provide access to
 * the options held inside this class.
 *
 * @author ncuesta
 */
abstract class BaseCustomOptionsHolder
{
  /**
   * Held options. This property has to be overridden in subclasses in order to
   * let the 'magic' work.
   * @var Array An associative array holding the option keys and values.
   */
  protected
    $_options = array();

  /**
   * Returns an instance of self, but no one can use it but us.
   *
   * @return BaseCustomOptionsHolder
   * @see is getInstance
   *
   */
  protected function  __construct() {  }

  /**
   *
   * @return BaseCustomOptionsHolder subclass of this class
   */
  public static function getInstance($subclass=null)
  {
    $instance = new $subclass();
    $instance instanceOf self;
    return $instance;
  }

  /**
   * Return the array of held options as an associative Array holding the
   * actual values as the array keys and the string representation for each item
   * as the value.
   *
   * @param mixed $include_blank Whether to add a blank entry at the beginning of the array.
   *        This value could either be a boolean or a string. If it is a boolean meaning True, a blank option will be added.
   *        If this value is a string, it will be used as the text for the blank option.
   * @return Array The array of options.
   */
  public function getOptions($include_blank = false)
  {
    if ($include_blank !== false && !is_null($include_blank))
    {
      return array('' => (is_string($include_blank) ? $include_blank : '')) + $this->_options;
    }

    return $this->_options;
  }

  /**
   * Return an array holding the keys of the held options Array.
   *
   * @return Array The array of keys for the held options.
   */
  public function getKeys()
  {
    return array_keys($this->getOptions());
  }

  /**
   * Return an array holding the values (a set of string) of the held options Array.
   *
   * @return Array The array of string values for the held options.
   */
  public function getValues()
  {
    return array_values($this->getOptions());
  }

  /**
   * Return the string representation for $key key. If $key is not a valid
   * option key, $default_value will be returned.
   *
   * @param mixed $key
   * @param String $default_value
   *
   * @return String The string representation for $key.
   */
  public function getStringFor($key, $default_value = null)
  {
    if (!in_array($key, $this->getKeys()))
    {
      return $default_value;
    }

    return $this->_options[$key];
  }
}