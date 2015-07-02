<?php

/**
 * dcWidgetI18nNumberInput
 *
 * @author ncuesta
 */
class dcWidgetI18nNumberInput extends sfWidgetFormInput
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  public function configure($options = array(), $attributes = array())
  {
    $this->addOption('help_message', 'Decimal separator can either be a comma (\',\') or a dot (\'.\'), but there must not be more than one');
    $this->addOption('help_class', 'help');
    $this->attributes['style'] = isset($this->attributes['style']) ? $this->attributes['style'].' text-align: right;' : 'text-align: right;';
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return parent::render($name, str_replace('.', ',', $value), $attributes, $errors).$this->getHelp();
  }

  protected function getHelp()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

    $helpMessage = $this->getOption('help_message');
    $helpClass   = $this->getOption('help_class');

    return empty($helpMessage)? '' : '<div class="'.$helpClass.'">'.__($helpMessage).'</div>';
  }
}
