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

class imWidgetFormPropelChoiceOrCreatePopUp extends sfWidgetFormPropelChoice
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addRequiredOption("url");
    $this->addOption("new_label", "New");
    $this->addOption("ws_url", "@pm_widget_form_propel_choice_or_create");
  }

  public function getJavaScripts()
  {
    return array("/js/choice_or_create_popup.js");
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('JavascriptBase');

    $widget = $this->getRenderer()->render($name, $value, $attributes, $errors);
    $space = " ";

    $model = $this->getOption("model");
    $widget_id = "#".$name;
    $widget_id = str_replace("[", "_", $widget_id);
    $widget_id = str_replace("]", "", $widget_id);
    $url = url_for($this->getOption("url"));
    $ws_url = url_for($this->getOption("ws_url"), true);
    $type = !$this->getOption('expanded') ? 'select' : ($this->getOption('multiple') ? 'checkbox' : 'radio');
    $link = link_to_function($this->getOption("new_label"), "linkToNew('$model', '$widget_id', '$url', '$ws_url', '$type', '$name');");

    return $widget.$space.$link;
  }
}