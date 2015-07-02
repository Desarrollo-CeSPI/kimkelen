<?php

/**
 * dcWidgetFormChangeForCredentials represents widget that shows diferent components 
 * depending on users credentials
 *
 * This widget needs some JavaScript to work. So, you need to include the JavaScripts
 * files returned by the getJavaScripts() method.
 *
 * If you use symfony 1.2, it can be done automatically for you.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Christian A. Rodriguez <car@cespi.unlp.edu.ar>
 * @version    SVN: $Id: $
 */
class dcWidgetFormChangeForCredentials extends sfWidgetForm
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * credentials:                  An array of required credentials. The syntax must be
   *                                  specified in the way described in Symphony's book 
   *                                  Chapter 6 
   *  * widget_without_credentials:   Widget to render when user has not required credentials 
   *  * widget_with_credentials:      Widget to render when user has required credentials 
   *  * label_change_widget_with_credentials: The button label to show widget with required 
   *                                          credentials
   *  * label_change_widget_without_credentials: The button label to show widget without 
   *                                             required credentials
   *  * can_change:                   button label will be shown? defaults to true. If false
   *                                  and users has credentials, widget with credentials will be
   *                                  rendered
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('credentials');
    $this->addRequiredOption('widget_without_credentials');
    $this->addRequiredOption('widget_with_credentials');

    $this->addOption('label_change_widget_with_credentials','Change to advanced mode');
    $this->addOption('label_change_widget_without_credentials','Change to normal mode');
    $this->addOption('can_change',true);

    $this->addOption('template', <<<EOF
<div>
  %hidden%
  <div style="float: left">

    <div id="%widget_without_id%">
      %widget_without%
    </div>

    <div id="%widget_with_id%" style="display: none">
      %widget_with%
    </div>

  </div>

  <div style="padding-left: 1em; float: left">
    <div id="%button_without_id%">
    %button_without%
    </div>
    <div id="%button_with_id%" style="display: none">
    %button_with%
    </div>
  </div>
  <script>
    WidgetChangeForCredentials.addObservers('%hidden_id%','%without_id%','%with_id%');
  </script>
</div>
EOF
);
  }

  private function getButtonWithoutCredentials($name)
  {
    return $this->canChange()?sprintf('<a href="#" onclick="%s">%s</a>', 'WidgetChangeForCredentials.toAdvancedMode(\''.$this->generateId($name).'\'); return false;', $this->translate($this->getOption('label_change_widget_with_credentials'))):'';
  }

  private function getButtonWithCredentials($name)
  {
    return sprintf('<a href="#" onclick="%s">%s</a>', 'WidgetChangeForCredentials.toNormalMode(\''.$this->generateId($name).'\'); return false;', $this->translate($this->getOption('label_change_widget_without_credentials')));
  }

  private function getWidgetWithoutCredentials($name,$value)
  {
    return (!$this->getOption('can_change')&&
         $this->hasCredential())?
      $this->getWidgetWithCredentials($name,$value):$this->getOption('widget_without_credentials')->render("without_$name",$value);
  }

  private function hasCredential()
  {
    return sfContext::getInstance()->getUser()->hasCredential($this->getOption('credentials'));
  }


  private function canChange()
  {
    return $this->getOption('can_change')&&$this->hasCredential();
  }

  private function getWidgetWithCredentials($name,$value)
  {
    return  $this->canChange()||(!$this->getOption('can_change')&&
            $this->hasCredential())?
            $this->getOption('widget_with_credentials')->render("with_$name",$value):'';
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $me= new sfWidgetFormInputHidden();
    $widget_1=$this->getWidgetWithoutCredentials($name,$value);
    $widget_2=$this->getWidgetWithCredentials($name,$value);
    $widget_without_id="div_without_".$this->generateId($name);
    $widget_with_id="div_with_".$this->generateId($name);
    $button_without_id="div_button_without_".$this->generateId($name);
    $button_with_id="div_button_with_".$this->generateId($name);
    return strtr($this->getOption('template'), array(
      '%hidden%'              => $me->render($name,$value),
      '%widget_without_id%'   => $widget_without_id,
      '%widget_without%'      => $widget_1,
      '%widget_with_id%'      => $widget_with_id,
      '%widget_with%'         => $widget_2,
      '%button_without_id%'   => $button_without_id,
      '%button_with_id%'      => $button_with_id,
      '%button_without%'      => $this->getButtonWithoutCredentials($name),
      '%button_with%'         => $this->getButtonWithCredentials($name),
      '%hidden_id%'           => "#".$this->generateId($name),
      '%without_id%'          => "#".$this->generateId("without_$name"),
      '%with_id%'             => "#".$this->generateId("with_$name"),
    ));
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array_merge(parent::getJavaScripts(), array('/dcReloadedFormExtraPlugin/js/change_for_credentials.js'));
  }

}
