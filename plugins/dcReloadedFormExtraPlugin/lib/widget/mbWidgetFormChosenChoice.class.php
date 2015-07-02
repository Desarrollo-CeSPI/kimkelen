<?php

class mbWidgetFormChosenChoice extends sfWidgetFormSelect
{
  /**
   * Configures the widget.
   *
   * Available options:
   *
   *  * value_widget:           The widget that holds the searched value
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('default_text', false);
    $this->addOption('no_results_text', null);
    $this->addOption('allow_single_deselect', false);
  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
    
    $attributes['class'] = 'chzn-select';

    if (false !== ($default_text = $this->getOption('default_text')))
    {
      $attributes['data-placeholder'] = __($default_text);
    }
    else
    {
      $text = $this->getOption('multiple') ? 'Select Some Options' : 'Select Some Option';
      
      $attributes['data-placeholder'] = __($text);
    }

    $no_results_text = $this->getOption('no_results_text') ? $this->getOption('no_results_text') : 'No results matches';
    $this->setOption('no_results_text', $this->translate($no_results_text));

    $rendered_widget = parent::render($name, $value, $attributes, $errors);

    return strtr('%rendered_widget%%chosen_initialization%', array(
      '%rendered_widget%' => $rendered_widget,
      '%chosen_initialization%' => $this->getChosenInitialization($this->generateId($name))
    ));
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array_merge(parent::getJavaScripts(), array("/dcReloadedFormExtraPlugin/js/chosen.jquery.js"));
  }
  
  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * The array keys are files and values are the media names (separated by a ,):
   *
   *   array('/path/to/file.css' => 'all', '/another/file.css' => 'screen,print')
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array("/dcReloadedFormExtraPlugin/css/chosen.css" => "all"));
  }
  
  public function getChosenInitialization($id)
  {
    $opts = array();
    foreach (array('no_results_text', 'allow_single_deselect') as $opt)
    {
      if ($this->getOption($opt))
      {
        if (is_string($this->getOption($opt)))
        {
          $value = '"'.$this->translate($this->getOption($opt)).'"';
        }
        elseif (is_bool($this->getOption($opt)))
        {
          $value = $this->getOption($opt) ? 'true' : 'false';
        }
        $opts[] = $opt.': '.$value;
      }
    }

    $options_str = !empty($opts) ? '{'.implode(', ', $opts).'}' : '';

    $js = <<<EOF
<script type="text/javascript">
  jQuery('#%s.chzn-select').chosen(%s);
</script>
EOF;

    return sprintf($js, $id, $options_str);
  }
}
