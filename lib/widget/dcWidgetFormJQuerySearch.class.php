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
 * pmWidgetFormJQuerySeaerch represents an HTML input tag with the search capability.
 *
 * @package    dcWidgetFormJQuerySearch
 * @subpackage widget
 * @author     Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class dcWidgetFormJQuerySearch extends sfWidgetForm
{
  /**
   * Configures the widget.
   *
   * Available options:
   *
   *  * value_widget:           The widget that holds the searched value
   *  * search_widget:          The widget that performs the search
   *  * selected_label:         The label for the selected value
   *  * search_label:           The label inside the search button
   *  * search_template:        The search template
   *                            The available placeholders are:
   *                              * selected_label
   *                              * preview_div_id
   *                              * value_widget
   *                              * value
   *                              * search_widget
   *                              * search_label
   *                              * update_div_id
   *                              * widget_initialization_js
   *                              * value
   *                              * js_var
   *  * results_partial              The partial that displays the results
   *  * select_image:           The image for selecting a result
   *  * deselect_image:         The image for deselecting a result
   *  * no_results_found_label: The text for empty results search
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addRequiredOption("url");
    $this->addOption("value_widget", new sfWidgetFormInputHidden());
    $this->addOption("search_widget", new sfWidgetFormInput());
    $this->addOption("selected_label", "Selected element");
    $this->addOption("search_label", "Search");
    $this->addOption("limit", 10);
    $this->addOption("search_template", <<<EOF
<div class="preview">%selected_label%: <span id="%preview_div_id%">%value%</span></div>
%value_widget%
%search_widget%
<button onclick="%js_var%.search(); return false;">%search_label%</button>
<div id="%update_div_id%"></div>
%widget_initialization_js%
EOF
    );
    $this->addOption("results_partial", "dc_ajax/pmWidgetFormJQuerySearch");
    $this->addOption("select_image", "/dcReloadedFormExtraPlugin/images/accept.png");
    $this->addOption("deselect_image", "/dcReloadedFormExtraPlugin/images/delete.png");
    $this->addOption("no_results_found_label", "No results found");
  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $search_template = $this->getOption("search_template");
    $value_widget = $this->getOption("value_widget");
    $search_widget = $this->getOption("search_widget");

    $html = strtr($search_template, array(
      "%value_widget%" => $value_widget->render($name, $value, $attributes, $errors),
      "%search_widget%" => $search_widget->render("search_".$name),
      "%value%" => $this->getValueString($value),
      "%search_label%" => __($this->getOption("search_label")),
      "%selected_label%" => __($this->getOption("selected_label")),
      "%update_div_id%" => "update_".$this->generateId($name),
      "%preview_div_id%" => "preview_".$this->generateId($name),
      "%widget_initialization_js%" => $this->getWidgetInitializationJS($name, $value),
      "%js_var%" => $this->generateId($name)."_pmWidgetFormJQuerySearch"
    ));

    return $html;
  }

  public function getJavaScripts()
  {
    return array("/dcReloadedFormExtraPlugin/js/jquery_search.js");
  }

  public function getStylesheets()
  {
    return array("/dcReloadedFormExtraPlugin/css/jquery_search.css" => "all");
  }

  public function getWidgetInitializationJS($name, $value)
  {
    $tpl = <<<EOF
<script>
  %js_var% = new pmWidgetFormJQuerySearch();
  %js_var%.url = "%url%";
  %js_var%.search_widget_id = "#%search_widget_id%";
  %js_var%.update_div_id = "#%update_div_id%";
  %js_var%.hidden_widget_id = "#%hidden_widget_id%";
  %js_var%.preview_div_id = "#%preview_div_id%";
  %js_var%.select_image = "%select_image%";
  %js_var%.deselect_image = "%deselect_image%";
  %js_var%.no_results_found_label = "%no_results_found_label%";
  %js_var%.serialized_options = %serialized_options%;
  %js_var%.js_var_name = "%js_var%";
  %deselect_link%;
</script>
EOF;

    return strtr($tpl, array(
      "%url%" => url_for($this->getOption("url")),
      "%search_widget_id%" => $this->generateId("search_".$name),
      "%hidden_widget_id%" => $this->generateId($name),
      "%preview_div_id%" => "preview_".$this->generateId($name),
      "%update_div_id%" => "update_".$this->generateId($name),
      "%select_image%" => public_path($this->getOption("select_image")),
      "%deselect_image%" => public_path($this->getOption("deselect_image")),
      "%no_results_found_label%" => __($this->getOption("no_results_found_label")),
      "%serialized_options%" => json_encode(base64_encode(serialize($this->getOptions()))),
      "%js_var%" => $this->generateId($name)."_pmWidgetFormJQuerySearch",
      "%deselect_link%" => $value ? $this->generateId($name)."_pmWidgetFormJQuerySearch.getDeselectLink()" : ""
    ));
  }

  public function getValueString($value)
  {
    return $value;
  }
}