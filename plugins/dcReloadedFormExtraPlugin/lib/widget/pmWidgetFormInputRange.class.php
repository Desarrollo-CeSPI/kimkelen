<?php

/**
 * sfWidgetFormDateRange represents a range widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmWidgetFormInputRange extends sfWidgetForm
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * from_input:  The from input widget (required)
   *  * to_input:    The to input widget (required)
   *  * template:    The template to use to render the widget
   *                 Available placeholders: %from_input%, %to_input%
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('from_input', new sfWidgetFormInputText());
    $this->addOption('to_input', new sfWidgetFormInputText());

    $this->addOption('template', 'from %from_input% to %to_input%');
  }

  /**
   * Renders the widget.
   *
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
    $values = array_merge(array('from' => '', 'to' => '', 'is_empty' => ''), is_array($value) ? $value : array());

    return strtr($this->translate($this->getOption('template')), array(
      '%from_input%'      => $this->getOption('from_input')->render($name.'[from]', $value['from']),
      '%to_input%'        => $this->getOption('to_input')->render($name.'[to]', $value['to']),
    ));
  }
}
