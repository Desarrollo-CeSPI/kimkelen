<?php

/**
 * dcWidgetFormFilterInputRange
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class dcWidgetFormFilterInputRange extends sfWidgetForm
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * from_label: The label to be used for 'from' field.
   *  * to_label:   The label to be used for 'to' field.
   *  * template:   The template to use to render the widget.
   *                Available placeholders: %from_field%, %to_field%, %from_label%, %to_label%
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
    
    $this->addOption('from_label', __('From'));
    $this->addOption('to_label', __('to'));
    $this->addOption('template', '%from_label%: %from_field%<br />%to_label%: %to_field%');
  }

  /**
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
    $values = array_merge(array('from' => '', 'to' => ''), is_array($value) ? $value : array());

    return strtr($this->getOption('template'), array(
      '%from_label%' => $this->getOption('from_label'),
      '%to_label%'   => $this->getOption('to_label'),
      '%from_field%' => $this->renderTag('input', array_merge(array('type' => 'text', 'id' => $this->generateId($name.'[from]'), 'name' => $name.'[from]', 'value' => $values['from']), $attributes)),
      '%to_field%'   => $this->renderTag('input', array_merge(array('type' => 'text', 'id' => $this->generateId($name.'[to]'), 'name' => $name.'[to]', 'value' => $values['to']), $attributes))
    ));
  }
}