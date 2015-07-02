<?php

/**
 * This widget adds a wrapper to the input with a certain ID. Example:
 *    <span id="wrapper_{$input_id}">
 *      $widget->render();
 *    </span>
 *
 * It can also render an ajax loader with the id "wrapper_loader_${input_id}" if the option 'provide_ajax_loader' is set to true.
 *
 * Options:
 *  'content'  : The content of the wrapper tag. If content is a widget, it will be rendered inside the wrapper tag. Defaults to: ''.
 *  'wrapper' : the kind of wrapper. Defaults to 'div'.
 *  'id'      : the id of the wrapper. Defaults to "wrapper_${input_id}"
 *  'ajax_loader_css' : the CSS of the Ajax loader. Defaults to: 'display: none; width: 20px; margin-left: 5px; margin-right: 5px; float: right;'
 *  'ajax_loader_id'  : the id ot the ajax loader. Defaults to: "wrapper_loader_${input_id}"
 *  'ajax_loader_url' : inside symfony path to the ajax loader image. Defaults to '/dcFormExtraPlugin/images/ajax-loader.gif'.
 *  'provide_ajax_loader' : if set to true, the ajax loader will be drawn.
 *
 */

class mtWidgetFormWrapper extends sfWidgetForm
{
  public function configure($options = array(), $attributes = array())
  {
    $this->addOption('content', '');
    $this->addOption('post_content', '');
    $this->addOption('wrapper', 'div');
    $this->addOption('id', null);
    $this->addOption('ajax_loader_css', 'display: none; width: 20px; margin-left: 5px; margin-right: 5px; float: right;');
    $this->addOption('ajax_loader_id', null);
    $this->addOption('ajax_loader_url',  '/dcFormExtraPlugin/images/ajax-loader.gif');
    $this->addOption('provide_ajax_loader', false);
    parent::configure($options, $attributes);
  }


  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Asset');

    $id   = $this->getOption('id')? $this->getOption('id') : 'wrapper_'.$this->generateId($name, $value);
    $html = '';
    if (!is_null($this->getOption('content')))
    {
      if ($this->getOption('content') instanceOf sfWidget)
      {
        $attrs = array_merge($this->attributes, $attributes);
        $html = $this->getOption('content')->render($name, $value, $attrs, $errors);
      }
      else
      {
        $html = $this->getOption('content');
      }
    }

    $loader_id   = $this->getOption('ajax_loader_id')? $this->getOption('ajax_loader_id') : 'wrapper_loader_'.$this->generateId($name, $value);
    $loader_path = image_path($this->getOption('ajax_loader_url'));
    $loader_html = $this->getOption('provide_ajax_loader')? '<img style="%%style%%" id="%%loader_id%%" src="%%loader_path%%" alt="ajax_loader">' : '';

    $loader_html = str_replace(array('%%loader_id%%', '%%loader_path%%', '%%style%%'), array($loader_id, $loader_path, $this->getOption('ajax_loader_css')), $loader_html);

    return str_replace(array('%%wrapper%%', '%%id%%', '%%html%%', '%%loader%%', '%%post_content%%'),
                       array($this->getOption('wrapper'), $id, $html, $loader_html, $this->getOption('post_content')),
                       '%%loader%%<%%wrapper%% id="%%id%%">%%html%%%%post_content%%</%%wrapper%%>');
  }

  public function setOption($name, $value)
  {
    if ($this->hasOption($name))
    {
      parent::setOption($name, $value);
    }
    elseif (is_object($this->getOption('content')) && method_exists($this->getOption('content'), 'setOption'))
    {
      $this->getOption('content')->setOption($name, $value);
    }
  }

  public function setOptions($options)
  {
    $this->options = array();
    foreach ($options as $name => $value)
    {
      $this->setOption($name, $value);
    }
  }

  public function getJavaScripts()
  {
    if ($this->getOption('content') instanceOf sfWidget && method_exists($this->getOption('content'), 'getJavaScripts'))
    {
      return $this->getOption('content')->getJavaScripts();
    }
    return array();
  }

  public function getStylesheets()
  {
    if ($this->getOption('content') instanceOf sfWidget && method_exists($this->getOption('content'), 'getStylesheets'))
    {
      return $this->getOption('content')->getStylesheets();
    }
    return array();
  }
}
