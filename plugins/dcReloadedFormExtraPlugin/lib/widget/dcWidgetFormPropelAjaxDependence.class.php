<?php
/**
 * dcWidgetFormPropelAjaxDependence is a subclass of dcWidgetFormAjaxDependence
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
class dcWidgetFormPropelAjaxDependence extends dcWidgetFormAjaxDependence 
{
  /* 
   * Available options:
   *
   *  * related_column:        Peer object column name so it can build proper criteria
   *
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','JavascriptBase','Url'));

    parent::configure($options, $attributes );
    $this->addRequiredOption('related_column');
    $this->addOption('get_observed_value_callback',array($this,'updateColumn'));
  }

  public function updateColumn($widget,$value)
  {
    $c=$widget->hasOption('criteria')?$widget->getOption('criteria'):null;
    $c=is_null($c)?new Criteria():$c;
    $c->add($this->getOption('related_column'),$value);
    $widget->setOption('criteria',$c);
  }
}