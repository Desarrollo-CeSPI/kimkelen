<?php

/**
 * ncWidgetFormPlainDate
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class ncWidgetFormPlainDate extends mtWidgetFormPlain
{
  protected function configure($options = array(), $attributes = array())
  {
    // The desired format for the date output
    $this->addOption('format', 'Y-m-d');
    // Whether the 'format' option should be passed as an argument to the callback
    // or if it should be formatted externally, using date() function.
    $this->addOption('format_is_argument', true);

    parent::configure($options, $attributes);

    $this->setOption('value_callback', array($this, 'formatDate'));
  }

  protected function retrieveValueAsArray($value)
  {
    $default = array('year' => null, 'month' => null, 'day' => null);

    if (is_array($value))
    {
      $value = array_merge($default, $value);
    }
    else
    {
      // convert value to an array
      $value = (string) $value == (string) (integer) $value ? (integer) $value : strtotime($value);

      if (false === $value)
      {
        $value = $default;
      }
      else
      {
        $value = array('year' => date('Y', $value), 'month' => date('n', $value), 'day' => date('j', $value));
      }
    }

    return $value;
  }

  protected function retrieveValue($value)
  {
    if (is_array($value))
    {
      $value = strtotime(sprintf('%04d-%02d-%02d', $value['year'], $value['month'], $value['day']));
    }
    else
    {
      $value = parent::retrieveValue($value);
    }

    return $value;
  }

  public function formatDate($date)
  {
    if (is_array($date) || is_int($date))
    {
      if (is_array($date))
      {
        $timestamp = strtotime(sprintf('%04d-%02d-%02d', $date['year'], $date['month'], $date['day']));
      }
      else
      {
        $timestamp = $date;
      }

      return date($this->getOption('format'), $timestamp);
    }

    if (false !== $this->getOption('format_is_argument'))
    {
      $this->setOption('method_args', $this->getOption('format'));
    }

    $date = parent::retrieveValue($date);

    if (false === $this->getOption('format_is_argument') && null !== $date)
    {
      $date = date($this->getOption('format'), $date);
    }

    return $date;
  }

  protected function renderHiddenField($name, $value, $attributes = array())
  {
    $input_hidden = new sfWidgetFormInputHidden(array(), $attributes);

    $values = $this->retrieveValueAsArray($value);

    return sprintf('%s%s%s',
      $input_hidden->render($name.'[year]', $values['year']),
      $input_hidden->render($name.'[month]', $values['month']),
      $input_hidden->render($name.'[day]', $values['day'])
    );
  }

  protected function renderDescription($name, $value, $content)
  {
    return $this->renderContentTag('span', $content, array('id' => $this->generateId($name, $value).'_description'));
  }

  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array('/dcIntegratorFormExtraPlugin/css/integrator_plain.css' => 'all'));
  }

}