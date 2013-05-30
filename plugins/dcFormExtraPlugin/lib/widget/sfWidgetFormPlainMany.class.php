<?php

/**
 * sfWidgetFormPlainText represents text
 *
 * Options:
 *   * object: An object to which 'method' will be sent to obtain the values.
 *   * method: A string with the name of the method to be called from 'object' to obtain the values.
 *   * collapse: A boolean or a string that determines whether the objects will be collapsed (initially hidden)
 *               or not. If this value is a string, it will be used as the toggle string.
 *               Available placeholders:
 *                 * %count% the number of values.
 *   * empty_value: A string to show when the value of the field is null.
 *   * value_callback: A valid Callback that should be used in order to obtain a string value for the field.
 *
 * @author ncuesta
 */
class sfWidgetFormPlainMany extends sfWidgetForm
{
  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('object', null);
    $this->addOption('method', null);
    $this->addOption('empty_value', '&nbsp;');
    $this->addOption('collapse', false);
    $this->addOption('restrict_size', false);
    $this->addOption('restricted_size', 125);
    $this->addOption('add_hidden_input', false);
    $this->addOption('hidden_input_value', null);
    $this->addOption('value_callback', null);
    $this->addOption('to_string_method', '__toString');

    parent::__construct($options, $attributes);
  }

  public function renderOne($value)
  {
    if (!is_null($this->getOption('value_callback')))
    {
      $string_value = call_user_func($this->getOption('value_callback'), $value);
    }
    else
    {
      $string_value = strval($value);
    }

    $method = $this->getOption('to_string_method');
    if (is_object($string_value) && (!is_null($method)))
    {
      $string_value = $string_value->$method();
    }
    return $string_value;
  }

  public function getCollapseSnippet($name, $count)
  {
    if (is_null($this->getOption('collapse')) || !$this->getOption('collapse'))
    {
      return null;
    }
    
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Javascript'));
    $id = $this->generateId($name);

    $snippet = link_to_function(
      $this->getCollapseText($count),
      sprintf("document.getElementById('%s').style.display = (document.getElementById('%s').style.display == 'none' ? 'block' : 'none'); this.style.display = 'none';", $id, $id)
    );

    $snippet .= javascript_tag(
      sprintf("document.getElementById('%s').style.display = 'none'", $id)
    );

    return $snippet;
  }

  public function getCollapseText($count)
  {
    $text = (is_string($this->getOption('collapse')) ? $this->getOption('collapse') : "Show all %count% elements");

    return str_replace('%count%', $count, $text);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

    $values = $value;
    $html = "";

    if (!(is_null($this->getOption('object')) || is_null($this->getOption('method'))))
      $values = call_user_func(array($this->getOption('object'), $this->getOption('method')));

    if (!is_array($values))
    {
      if (!is_null($values))
      {
        $values = array($values);
      }
      else
      {
        $values = array();
      }
    }

    

    if (count($values) > 0)
    {
      $tableId = '';
      //Starts the 'scrolled div'
      if ($this->getOption('restrict_size'))
      {
        $html .= '<div id="'.$this->generateId($name).'" style="height: '.$this->getOption('restricted_size').'px; overflow-x:hidden; overflow-y:auto">';
      }
      else { $tableId = 'id="'.$this->generateId($name).'"'; }

      $html .= "<table $tableId><tbody>";
      foreach ($values as $v)
      {
        $html.="<tr><td>".$this->renderOne($v)."</td></tr>";
      }
      $html.= "</tbody></table>";

      //Ends the scrolled div
      if ($this->getOption('restrict_size'))
      {
        $html .= '</div>';
      }
      $html .= $this->getCollapseSnippet($name, count($values));
    }
    else
    {
      $html = __($this->getOption('empty_value'));
    }


    if ($this->getOption('add_hidden_input'))
    {
      $html .= $this->renderHiddenInput($name, $this->getOption('hidden_input_value'));
    }


    return $html;
  }

  protected function renderHiddenInput($name, $values)
  {
    if (is_array($values) && count($values) > 0)
    {
      $hidden = new sfWidgetFormSelect(array('multiple' => true, 'is_hidden' => true, 'choices' => $values), array('style' => 'display: none'));
    }
    else
    {
      $hidden = new sfWidgetFormInputHidden();
    }
    return $hidden->render($name, $values);
  }
}
