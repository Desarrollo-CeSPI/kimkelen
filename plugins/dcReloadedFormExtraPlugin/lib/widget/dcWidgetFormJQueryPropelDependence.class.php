<?php
/**
 * dcWidgetFormJQueryPropelDependence is an speciallized version of it's parent
 * It provides an easy way to apply the dependency with propel objects.
 * We add a custom on_change callback handler to trap the event of changing
 * related objects value and updating our value adding a criteria condition to
 * related widget.
 * Options:
 *  * related_column:   Associative array with keys being observed html id and
 *                      values, the corresponding observed value
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class dcWidgetFormJQueryPropelDependence extends dcWidgetFormJQueryDependence {


  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes );
    $this->addRequiredOption('related_column');
    $this->addOption('on_change',array(__CLASS__,'updateColumn'));
    $this->addOption('observed_is_multiple', array());
    $this->addOption('criteria', null);
  }

  /**
   * Custom callback handler. You can overwrite it as your needs
   *
   * @param dcWidgetFormJQueryDependence $widget_dependece
   * @param array $values
   */
  static public function updateColumn(dcWidgetFormJQueryDependence $widget_dependece,$values)
  {
    $multiple = $widget_dependece->getOption('observed_is_multiple');
    $widget = $widget_dependece->getOption('widget');
    $c=$widget->hasOption('criteria')?$widget->getOption('criteria'):null;
    $c=is_null($c)?new Criteria():$c;
    foreach ($widget_dependece->getOption('related_column') as $id => $column)
    {
      if (!array_key_exists($id, $values))
      {
        throw new LogicException ("Index $id not found in received values");
      }
      if (!empty($values[$id]))
      {
        $crit = $c->getNewCriterion($column, $values[$id], (isset($multiple[$id]) && $multiple[$id]? Criteria::IN : Criteria::EQUAL));

        if ($widget_dependece->getOption('or_null'))
        {
          $crit2 = $c->getNewCriterion($column, null, Criteria::ISNULL);
          $crit->addOr($crit2);
        }

        $c->addAnd($crit);
      }
    }
    $widget->setOption('criteria',$c);
  }
}
?>
