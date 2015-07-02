<?php

class pmWidgetFormPropelInputByCode extends sfWidgetFormInputText
{
  /**
   * Configures the current widget.
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addRequiredOption('column');
    $this->addOption('method', '__toString');
    $this->addOption('criteria', null);
    $this->addOption('peer_method', 'doSelectOne');
    $this->addOption('object_not_found_text', 'Object not found!');
    $this->addOption('template', <<<EOF
<span id="%s"></span>
<script type="text/javascript">
  $(document).ready(function()
  {
    $("#%s").check_for_code('%s', '%s');
  });
</script>
EOF
    );

    parent::configure($options, $attributes);
  }
  
  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $url = url_for('@pm_widget_form_propel_input_by_code', true);
    
    $options_without_template = $this->getOptions();
    unset($options_without_template['template']);
    
    return parent::render($name, $value, $attributes, $errors).
           sprintf($this->getOption('template'), $this->generateId($name.'_result'), $this->generateId($name), $url, serialize($options_without_template));
  }
  
  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array('/dcReloadedFormExtraPlugin/css/pm_widget_form_propel_input_by_code.css' => 'all');
  }
  
  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavascripts()
  {
    return array('/dcReloadedFormExtraPlugin/js/pm_widget_form_propel_input_by_code.js');
  }
}
