<?php
 /**  
  *
  *
  * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
  */
class crWidgetFormSelectableWidget extends sfWidgetForm {
  /**
   * Constructor options
   *
   * Required options:
   *
   * Available options:
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array()) {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('JavascriptBase'));

    $this->addRequiredOption('widgets');
    $this->addRequiredOption('default_widget');
    $this->addOption('cookie_name', 'cr_widget_form_selectable_widget');
    $this->addOption('cookie_expires', 365);
    $this->addOption('url', url_for('@crSelectableWidget'));
  }

  /* Have to overwite because we try to validate received options
   *
   */
  public function __construct($options = array(), $attributes = array())
  {
    parent::__construct($options, $attributes);
    $this->validateOptions();
  }

  private function validateOptions() {
    $this->widgets = $this->getOption('widgets');
    if ( ! is_array($this->widgets) ) {
      throw new LogicException('widgets option must be an array of widgets');
    }
    if ( !array_key_exists($this->getOption('default_widget'), $this->widgets)) {
      throw new LogicException('default_widget option must be an index of widgets option');
    }
  }


  /**
   * Merge javascripts for each selectable widget
   *
   * @return array
   */
  public function getJavaScripts() {
    $ret = array_merge(parent::getJavaScripts(), array('/dcReloadedFormExtraPlugin/js/jquery.cookie.js'));
    foreach ($this->widgets as $k=>$w) {
      $ret = array_merge( $ret, $w->getJavaScripts());
    }
    return $ret; 
  }

  /**
   * Merge stylesheets for each selectable widget
   *
   * @return array
   */
  public function getStylesheets() {
    $ret = array_merge(parent::getStylesheets(), array('/dcReloadedFormExtraPlugin/css/cr_widget_form_selectable_widget.css'=>'all'));
    foreach ($this->widgets as $k=>$w) {
      $ret = array_merge( $ret, $w->getStylesheets());
    }
    return $ret; 
  }

  protected function getCookieName($name) {
    return sprintf("%s_%s", $this->getOption('cookie_name'), $this->generateId($name));
  }

  protected function getCurrentWidgetKey($name) {
    $cookie = null;
    $request = sfContext::hasInstance('frontend')? sfContext::getInstance()->getRequest():null;
    if ($request !=null) {
      $cookie = $request->getCookie( $this->getCookieName( $name));
    }
    if ( ($cookie != null) && array_key_exists($cookie, $this->widgets) ) {
      return $cookie;
    }
    return $this->getOption('default_widget');
  }

  protected function getCurrentWidget( $name) {
    return $this->widgets[$this->getCurrentWidgetKey($name)];
  }

  protected function getJsClickAction($name, $widget_index, $value, $attributes, $errors) {
    $widget = $this->widgets[$widget_index];
    $data = sprintf("{ widget: '%s'}", base64_encode($widget->render($name, $value, $attributes, $errors)));
    return strtr(" jQuery.ajax({
      url:    '%url%',
      data:   %data%,
      type:   'POST',
      success: function success(data) { 
        jQuery('#%id%').html(data); 
        jQuery.cookie('%cookie%','%cookie_value%', { expires: %expires% , path: '/'});
      }
    })",array(
      '%url%'           =>  $this->getOption('url'),
      '%id%'            =>  sprintf("%s_widget_%s", $this->getPrefix(), $this->generateId($name)),
      '%data%'          =>  $data,
      '%cookie%'        =>  $this->getCookieName($name),
      '%cookie_value%'  =>  $widget_index,
      '%expires%'       =>  $this->getOption('cookie_expires'),
    ));
  }

  protected function getWidgetChanger($name, $value, $attributes, $errors) {
    $ret = '';
    foreach($this->widgets as $k => $w) {
        $ret.= strtr('<input type="radio" name="%id%" id="%name%" %checked% onclick="%js_click_action%"><label for="%name%">%label_name%</label>', array(
          '%js_click_action%'     => $this->getJsClickAction($name, $k, $value, $attributes, $errors),
          '%name%'                => $this->generateId($name).'_'.$k,
          '%label_name%'          => $k,
          '%id%'                  => 'changer_'.$this->generateId($name),
          '%checked%'             => $k == $this->getCurrentWidgetKey($name) ? 'checked="checked"' : '',
        ));
    }
    return sprintf('<div class="changer" id="changer_%s">%s</div>', $this->generateId($name), $ret);
  }


  /**
  * Prefix used for html ids needed for crWidgetFormSelectableWidget to work
  *
  * @return array
  */
  protected function getPrefix() {
    return 'cr_selectable';
  }

 /**
   * Renders a HTML content tag.
   *
   * @param string $name
   * @param <type> $value
   * @param array $attributes
   * @param array $errors
   */
  public function render($name, $value = null, $attributes = array(), $errors = array()) {
    $display_container_id = sprintf("%s_display_%s", $this->getPrefix(), $this->generateId($name));
    $widget_container_id  = sprintf("%s_widget_%s", $this->getPrefix(), $this->generateId($name));
    return
        strtr('<div class="cr_selectable_widget" id="%display_container%">%changer%<div id="%widget_container%">%widget%</div></div><script>$("#changer_%id%").buttonset()</script>',array(
              '%display_container%' =>  $display_container_id,
              '%changer%'           =>  $this->getWidgetChanger( $name, $value, $attributes, $errors),
              '%widget_container%'  =>  $widget_container_id,
              '%id%'                =>  $this->generateId($name),
              '%widget%'            =>  $this->getCurrentWidget( $name)->render($name, $value, $attributes, $errors) ,
        ));
  }
}
