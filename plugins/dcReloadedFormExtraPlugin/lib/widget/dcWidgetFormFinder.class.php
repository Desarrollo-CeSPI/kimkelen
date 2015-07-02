<?php

/**
 * dcWidgetFormFinder
 *
 */
class dcWidgetFormFinder extends sfWidgetForm
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addRequiredOption('form_class');
    $this->addRequiredOption('url');
    
    $this->addOption('criteria', null);
    $this->addOption('limit', 20);
    $this->addOption('multiple', false);
    $this->addOption('delete_image', false);
    $this->addOption('delete_text', 'Are you sure?');
    $this->addOption('submit_label', 'Search');
    $this->addOption('submit_label_params', array());
    $this->addOption('loader', 'Please wait...');

    $this->addOption('value_callback', array($this, 'defaultValueCallback'));
    $this->addOption('default_value', 'No value selected');
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $template = <<<HTML
<div class="dc_widget_form_finder" id="%id%_container">
  %field%

  <div class="dc_widget_form_finder_selection_container">
    <span class="dc_widget_form_finder_selection" onclick="jQuery('#%id%_form').fadeToggle(500)">%value%</span>
  </div>

  <div class="dc_widget_form_finder_form" id="%id%_form">
    %form%

    <input type="button" class="dc_widget_form_finder_submit" id="%id%_submit" value="%submit_label%" />
    <span class="dc_widget_form_finder_loader" id="%id%_loader" style="display: none;">
      %loader%
      <input type="button" class="dc_widget_form_finder_cancel" value="%cancel_label%" onclick="dcWidgetFormFinder.cancel(this); return false;" />
    </span>
    <span class="dc_widget_form_finder_error" id="%id%_error" style="display: none;">
      %error%
    </span>

    <div class="dc_widget_form_finder_results" id="%id%_results" style="display: none;">
      <div class="dc_widget_form_finder_hint">
        %hint%
      </div>
    </div>
  </div>
</div>
<style type="text/css">
#%id%_form { display: %form_display%; }
#%id%_collapser { display: %collapser_display%; }
</style>
<script type="text/javascript">
//<![CDATA[
jQuery(function() {
%javascript%
});
//]]>
</script>
HTML;

    return strtr($template, array(
      '%field%'             => $this->renderField($name, $value),
      '%value%'             => $this->renderValue($name, $value),
      '%hide_text%'         => $this->translate('Hide search form'),
      '%show_text%'         => $this->translate('Show search form'),
      '%form_display%'      => $value ? 'none' : 'block',
      '%collapser_display%' => $value ? 'inline-block' : 'none',
      '%selection_label%'   => $this->translate('Current selection:'),
      '%id%'                => $this->generateId($name),
      '%form%'              => $this->renderForm($name),
      '%loader%'            => $this->translate($this->getOption('loader')),
      '%submit_label%'      => $this->translate($this->getOption('submit_label'), $this->getOption('submit_label_params')),
      '%cancel_label%'      => $this->translate('Abort search'),
      '%error%'             => $this->translate('An error has occurred. Please try again later.'),
      '%javascript%'        => $this->renderJavascript($name),
      '%hint%'              => $this->renderHint()
    ));
  }

  /**
   * Render the underlying form.
   *
   * @param  string $name   The name of this widget.
   * @param  array  $values The values for the underlying form.
   *
   * @return string
   */
  protected function renderForm($name, $values = array())
  {
    $form = $this->getForm($values);
    
    $form->getWidgetSchema()->setNameFormat($this->generateId($name).'[%s]');

    $form->getWidgetSchema()->addFormFormatter('finder', $this->getFormFormatter($form->getWidgetSchema()));

    return $form->renderUsing('finder');
  }
  
  protected function renderField($name, $value)
  {    
    if ($this->getOption('multiple'))
    {
      if (!is_array($value))
      {
        $value = null !== $value ? array($value) : array();
      }

      $content = '';

      if (count($value) > 0) 
      {        
        foreach ($value as $val)
        {
          $content .= $this->renderContentTag('option', $val, array('selected' => true, 'value' => $val));
        }
      }      
      return $this->renderContentTag('select', $content, array('id' => $this->generateId($name), 'name' => $name. '[]', 'multiple' => true, 'style' => 'display: none'));
    }
    else
    {
      return $this->renderTag('input', array('type' => 'hidden', 'name' => $name, 'id' => $this->generateId($name), 'value' => $value));
    }
  }

  protected function renderHint()
  {
    if ($limit = $this->getOption('limit'))
    {
      return $this->translate("Search results have been limited to %limit%. If you don't see an expected result, try refining your search.", array('%limit%' => $limit));
    }
  }

  /**
   * Render the current value (selection) using the configured value callback.
   *
   * @param  mixed $value The current value.
   *
   * @return string
   */
  protected function renderValue($name, $value)
  {    

    if ($this->getOption('multiple'))
    {
      if (!is_array($value))
      {
        $value = null !== $value ? array($value) : array();
      }

      $hidden = count($value) ? 'style="display:none"':'';
      $content = '<span class="dc_widget_form_finder_selection_item unselected"' . $hidden. '>'. $this->translate($this->getOption('default_value')) .'</span>'; 

      foreach ($value as $val)
      {
        if (false !== $this->getOption('value_callback') && null !== $val)
        {
          $v = call_user_func($this->getOption('value_callback'), $val);
        }
        else
        {
          $v = $val;
        }
        
        $content .= '<span class="dc_widget_form_finder_selection_item">'. $v .'</span><a class="dc_widget_form_finder_selection_delete" id="item_'.$val.'"><img src="'. $this->getOption('delete_image') .'" /></a><br/>';
        $content .= '<script type="text/javascript">jQuery("#item_'.$val.'").data("related_id", '.$val.');</script>';
      }
      
      return $content;
    }
    else
    {
      if (false !== $this->getOption('value_callback') && null !== $value)
      {
        $value = call_user_func($this->getOption('value_callback'), $value);
      }
      
      if (trim($value) != '')
      {
        $content = strtr(<<<EOF
<span class="dc_widget_form_finder_selection_item">%value%</span>
<script>
var del = jQuery('<a class="dc_widget_form_finder_selection_single_delete"><img src="%delete_image%"/></a>');
del.click(function()
{
  jQuery('#%id%_container .dc_widget_form_finder_selection').html('<span class="dc_widget_form_finder_selection_item unselected">%default_text%</span>');
  jQuery('#%id%').val('').change();
  $(this).detach();
});
jQuery('#%id%_container .dc_widget_form_finder_selection').parent('div.dc_widget_form_finder_selection_container').append(del);
</script>
EOF
          , array(
            '%value%' => $value,
            '%delete_image%' => $this->getOption('delete_image'),
            '%id%' => $this->generateId($name),
            '%default_text%' => $this->translate($this->getOption('default_value'))
          ));
      }
      else
      {
        $content = sprintf('<span class="dc_widget_form_finder_selection_item unselected">%s</span>', $this->translate($this->getOption('default_value')));
      }
      
      return $content;
    }
  }

  /**
   * Render the javascript code needed for setting up this widget.
   *
   * @param  string $name The name of this widget.
   *
   * @return string
   */
  protected function renderJavascript($name)
  {
    $js = <<<JAVASCRIPT
dcWidgetFormFinder.register('#%id%_container', {
  url: '%url%',
  submit: jQuery('#%id%_container .dc_widget_form_finder_submit'),
  loader: jQuery('#%id%_container .dc_widget_form_finder_loader'),
  results: jQuery('#%id%_results'),
  form: jQuery('#%id%_form'),
  field: jQuery('#%id%'),
  selection: jQuery('#%id%_container .dc_widget_form_finder_selection'),
  collapser: jQuery('#%id%_collapser'),
  error: jQuery('#%id%_error'),
  default_text: '%default_text%',
  delete_text: '%delete_text%',
  delete_image: '%delete_image%',
  multiple: %multiple%
});
JAVASCRIPT;

    return strtr($js, array(
      '%name%' => $name,
      '%id%'   => $this->generateId($name),
      '%url%'  => url_for($this->getOption('url').'?form_namespace='.$this->generateId($name)),
      '%default_text%' => $this->translate($this->getOption('default_value')),
      '%delete_text%' => $this->translate($this->getOption('delete_text')),
      '%delete_image%' => $this->getOption('delete_image'),
      '%multiple%' => $this->getOption('multiple') ? 'true' : 'false'
    ));
  }

  /**
   * Get the form formatter for the underlying form.
   *
   * @param  sfWidgetFormSchema $widget_schema The widget schema of the form.
   *
   * @return dcWidgetFormSchemaFormatterFinder
   */
  protected function getFormFormatter(sfWidgetFormSchema $widget_schema)
  {
    return new dcWidgetFormSchemaFormatterFinder($widget_schema);    
  }

  /**
   * Get the form to be rendered inside this widget.
   *
   * @param  array $values The values for the form.
   *
   * @return sfFormFilterPropel
   */
  protected function getForm($values = array())
  {
    $form_class = $this->getOption('form_class');

    $form = new $form_class($values);

    // Add a '_model' field to $form and set it to the 'model' option
    $form->setWidget('_model', new sfWidgetFormInputHidden());
    $form->setValidator('_model', new sfValidatorPass());
    $form->setDefault('_model', $this->getOption('model'));

    // Add a '_limit' field to $form if a limit option has been set
    if ($this->getOption('limit'))
    {
      $form->setWidget('_limit', new sfWidgetFormInputHidden());
      $form->setValidator('_limit', new sfValidatorInteger(array('min' => 1)));
      $form->setDefault('_limit', $this->getOption('limit'));
    }
    
    if ($this->getOption('criteria'))
    {
      $form->setWidget('_criteria', new sfWidgetFormInputHidden());
      $form->setValidator('_criteria', new sfValidatorPass());
      $form->setDefault('_criteria', base64_encode(serialize($this->getOption('criteria'))));
    }

    $form->getWidgetSchema()->setNameFormat('dc_widget_form_finder[%s]');
    //$form->disableCSRFProtection();

    return $form;
  }

  public function getJavascripts()
  {
    $form_class = $this->getOption('form_class');
    $form = new $form_class();
    
    return array_merge($form->getJavaScripts(), array('/dcReloadedFormExtraPlugin/js/dc_widget_form_finder.js'));
  }

  public function getStylesheets()
  {
    $form_class = $this->getOption('form_class');
    $form = new $form_class();
    
    return array_merge($form->getStylesheets(), array('/dcReloadedFormExtraPlugin/css/dc_widget_form_finder.css' => 'all'));
  }

  public function defaultValueCallback($id)
  {
    $object = call_user_func(array($this->getOption('model'), 'retrieveByPK'), $id);

    return strval($object);
  }
  
  /**
   * Translates the given text.
   *
   * @param  string $text       The text with optional placeholders
   * @param  array $parameters  The values to replace the placeholders
   *
   * @return string             The translated text
   *
   * @see sfWidgetFormSchemaFormatter::translate()
   */
  protected function translate($text, array $parameters = array())
  {
    if (null === $this->parent)
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
      
      return __($text, $parameters);
    }
    else
    {
      return $this->parent->getFormFormatter()->translate($text, $parameters);
    }
  }
}
