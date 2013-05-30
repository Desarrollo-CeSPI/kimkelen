<?php
/**
 * dcWidgetAjaxDependencePropel is a subclass of dcWidgetAjaxDependence
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


class dcWidgetFormAjaxDependencePropel extends dcWidgetFormAjaxDependence
{
  /* 
   * Available options:
   *
   *  * related_column:        Peer object column name so it can build proper criteria
   *
   *  * observed_is_multiple:  Array of html ids which inputs have multiple values.
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
    $this->addOption('observed_is_multiple', false);
  }

  public function updateColumn($widget,$value)
  {
    $c=$widget->hasOption('criteria')?$widget->getOption('criteria'):null;
    $c=is_null($c)?new Criteria():$c;
    $value = $this->getOption('observed_is_multiple') && !is_array($value)? explode(',', $value) : $value;
    $c->add($this->getOption('related_column'),$value, Criteria::IN);
    $widget->setOption('criteria',$c);
  }
}
