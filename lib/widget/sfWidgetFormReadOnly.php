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
 * sfWidgetFormReadOnly represents a read_only field with a hidden input holding
 * its actual value.
 *
 * Options:
 *   * empty_value: A string to show when the value of the field is null.
 *   * value_callback: A valid Callback that should be used in order to obtain a string value for the field.
 *   * plain: A boolean indicating whether this widget should be plain text or include a hidden input.
 *
 * @author ncuesta
 */
class sfWidgetFormReadOnly extends sfWidgetForm
{
  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('empty_value', '&nbsp;');
    $this->addOption('value_callback', null);
    $this->addOption('plain', true);

    parent::__construct($options, $attributes);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $html = '';

    if (!$this->getOption('plain'))
    {
      $input_hidden = new sfWidgetFormInputHidden();
      $html .= $input_hidden->render($name, $value);
    }

    $clean_value = (is_null($value) || trim($value) == '' ? $this->getOption('empty_value') : $value);

    if (!is_null($this->getOption('value_callback')))
    {
      $formatted_value = call_user_func($this->getOption('value_callback'), $value);
      $html .= (is_null($formatted_value) ? $clean_value : $formatted_value);
    }
    else
    {
      $html .= $clean_value;
    }

    return $html;
  }
}