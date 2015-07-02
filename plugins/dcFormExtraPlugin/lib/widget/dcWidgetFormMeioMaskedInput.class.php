<?php

/**
 * dcWidgetFormMeioMaskedInput
 *
 * Input wrapper for use with meioMask js.
 *
 * @author ncuesta
 */
class dcWidgetFormMeioMaskedInput extends sfWidgetFormInput
{
  protected $default_masks = array(
      'phone',
      'phone-us',
      'cpf',
      'cpnj',
      'date',
      'date-us',
      'cep',
      'time',
      'cc',
      'integer',
      'decimal',
      'decimal-us',
      'signed-decimal',
      'signed-decimal-us'
    );
  
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * mask: The mask to be used, as defined by meioMask documentation
   * @see http://www.meiocodigo.com/projects/meiomask/#mm_masks
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  public function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('mask');
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
    if (!(strpos($this->getOption('mask'), 'decimal') === false))
    {
      $value = sprintf("%01.2f", $value);
    }
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'].' dc_widget_meio_masked' : 'dc_widget_meio_masked';

    $mask_name = $this->isCustomMask() ? 'custom_'.$this->generateId($name).'_mask' : $this->getOption('mask');
    
    return $this->getCustomMaskJavascript($mask_name).parent::render($name, $value, array_merge($attributes, array('alt' => $mask_name)), $errors);
  }

  /**
   * Answer whether the mask provided through 'mask' option is a custom mask or not.
   *
   * @return Boolean True if 'mask' option is a custom mask.
   */
  protected function isCustomMask()
  {
    return (!in_array($this->getOption('mask'), $this->default_masks));
  }

  /**
   * If the provided 'mask' option is a custom mask, return a snippet
   * of Javascript code adding that custom mask to the masks defined
   * by meioMask named after $mask_name parameter.
   * If the mask is not a custom one, a null string will be returned.
   *
   * @param String $mask_name The name for the custom mask (if applies).
   * @return String The snippet or null.
   */
  protected function getCustomMaskJavascript($mask_name)
  {
    if ($this->isCustomMask())
    {
      return '<script type="text/javascript">jQuery.mask.masks.'.$mask_name.' = { mask: "'.$this->getOption('mask').'" }</script>';
    }

    return;
  }

  public function getJavascripts()
  {
    return array_merge(parent::getJavaScripts(), array('/dcFormExtraPlugin/js/jquery.meio.mask.js', '/dcFormExtraPlugin/js/dcWidgetFormMeioMasked.js'));
  }
}
