<?php

class pmWidgetFormDoctrineChoiceOrCreate extends sfWidgetFormDoctrineChoice
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addRequiredOption("url");
    $this->addOption("new_label", "New");
    $this->addOption("ws_url", "@pm_widget_form_doctrine_choice_or_create");
  }

  public function getJavaScripts()
  {
    return array("/dcReloadedFormExtraPlugin/js/choice_or_create.js");
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