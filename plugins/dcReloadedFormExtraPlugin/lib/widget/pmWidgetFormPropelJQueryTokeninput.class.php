<?php

class pmWidgetFormPropelJQueryTokeninput extends sfWidgetFormPropelChoice
{
  public function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addRequiredOption('column');
    $this->addOption('url', '@pm_widget_form_propel_jquery_tokeninput');
    $this->addOption('method', '__toString');
    $this->addOption('key_method', 'getPrimaryKey');
    $this->addOption('criteria', null);
    $this->addOption('retrieve_method', 'retrieveByPK');
    // AUN NO FUNCIONA MULTIPLE
    $this->addOption('multiple', false);
    $this->addOption('peer_method', 'doSelect');
    $this->addOption('config', '{}');

    parent::configure($options, $attributes);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $template = <<<EOF
<input type="hidden" id="%id%" name="%name%">
<input type="text" id="%id%_tokeninput" name="%name%">
<script>
$('#%id%_tokeninput').tokenInput('%url%', $.extend(
  {},
  {
    method: 'POST',
    onAdd: function(item)
    {
      $('#%id%').val(item.id);

      count = $('#%id%_tokeninput').prev().children('[class^=token-input-token]').length;

      $('#%id%').change();

      if (count > 1)
      {
        first_child = $('#%id%_tokeninput').prev().children('[class^=token-input-token]').first();
        $('#%id%_tokeninput').tokenInput('remove', { name: first_child.children('p').first().html() });
      }
    }
  },
  %config%
));
%default_value_template%
</script>
EOF;

    $default_value_template = "$('#%id%_tokeninput').tokenInput('add', { id: %value_id%, name: \"%value_string%\" });";


    if (!is_null($default_value_template))
    {
      $object = call_user_func(array($this->getOption('model').'Peer', $this->getOption('retrieve_method')), $value);

      $default_value_template = strtr($default_value_template, array(
        '%id%' => $this->generateId($name),
        '%value_id%' => $value,
        '%value_string%' => strval($object)
      ));
    }

    return strtr($template, array(
      '%id%' => $this->generateId($name),
      '%name%' => $name,
      '%url%' => url_for($this->getOption('url').'?serialized_widget_options='.base64_encode(serialize($this->getOptions()))),
      '%config%' => $this->getOption('config'),
      '%default_value_template%' => (is_null($value) || $value == '') ? '' : $default_value_template
    ));
  }

  public function getJavaScripts()
  {
    return array_merge(parent::getJavaScripts(), array('/dcReloadedFormExtraPlugin/js/jquery.tokeninput.js'));
  }

  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array(
      '/dcReloadedFormExtraPlugin/css/token-input.css' => 'all',
      '/dcReloadedFormExtraPlugin/css/token-input-facebook.css' => 'all',
      '/dcReloadedFormExtraPlugin/css/token-input-mac.css' => 'all'
    ));
  }
}

