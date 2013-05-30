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
 * pmWidgetFormPropelJQuerySearch represents an HTML input tag with the search capability.
 *
 * @package    dcWidgetFormJQuerySearch
 * @subpackage widget
 * @author     Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class dcWidgetFormPropelJQuerySearch extends dcWidgetFormJQuerySearch
{
  /**
   * Configures the widget.
   *
   * Available options:
   *
   *  * model:       The model class (required)
   *  * column:      The column (or columns) for performing the search (required)
   *  * method:      The method to use to display object values (__toString by default)
   *  * key_method:  The method to use to display the object keys (getPrimaryKey by default)
   *  * order_by:    An array composed of two fields:
   *                   * The column to order by the results (must be in the PhpName format)
   *                   * asc or desc
   *  * criteria:    A criteria to use when retrieving objects
   *  * connection:  The Propel connection to use (null by default)
   *  * peer_method: The peer method to use to fetch objects
   *
   * @see pmWidgetFormJQuerySearch
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addRequiredOption('model');
    $this->addRequiredOption('column');
    $this->addOption('method', '__toString');
    $this->addOption('key_method', 'getPrimaryKey');
    $this->addOption('retrieve_object_method', 'retrieveByPk');
    $this->addOption('order_by', null);
    $this->addOption('criteria', null);
    $this->addOption('connection', null);
    $this->addOption('peer_method', 'doSelect');

    $this->setOption("url", "@pm_widget_form_propel_jquery_search");
  }

  public function getValueString($value)
  {
    if(is_null($value)){
      return '';
    }
    $class = constant($this->getOption("model")."::PEER");
    $object = call_user_func(array($class,  $this->getOption('retrieve_object_method')), $value);
    $method = $this->getOption("method");

    return !is_null($object) ? parent::getValueString($object->$method()) : "";
  }
}