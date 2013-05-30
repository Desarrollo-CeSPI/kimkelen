<?php

class dcWidgetFormChoiceReadOnly extends sfWidgetFormChoice
{
  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('empty_value', '&nbsp;');
    $this->addOption('collapse', false);
    $this->addOption('restrict_size', false);
    $this->addOption('restricted_size', 125);
    $this->addOption('add_hidden_input', true);

    parent::__construct($options, $attributes);

    $this->setOption('multiple', true);
  }


  public function renderOne($value)
  {
    return $value;
  }

  public function generateTableId($name)
  {
    return '_table_'.$this->generateId($name);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $html = '';
    if (is_array($value) && count($value) > 0)
    {
      $tableId = '';
      //Starts the 'scrolled div'
      if ($this->getOption('restrict_size'))
      {
        $html .= '<div id="'.$this->generateId($name).'" style="height: '.$this->getOption('restricted_size').'px; overflow-x:hidden; overflow-y:auto">';
      }
      else { $tableId = $this->generateTableId($name); }

      $html .= "<table id=\"$tableId\"><tbody>";
      foreach ($this->getOption('choices') as $k => $v)
      {
        if (in_array($k, $value))
        {
          $html.="<tr><td>".$this->renderOne($v)."</td></tr>";
        }
      }
      $html.= "</tbody></table>";

      //Ends the scrolled div
      if ($this->getOption('restrict_size'))
      {
        $html .= '</div>';
      }
      $html .= $this->getCollapseSnippet($name, count($value));
    }
    else
    {
      $html = __($this->getOption('empty_value'));
    }


    if ($this->getOption('add_hidden_input'))
    {
      $html .= $this->renderHiddenInput($name, $value);
    }

    return $html;
  }

  public function getCollapseText($count)
  {
    $text = (is_string($this->getOption('collapse')) ? $this->getOption('collapse') : "Show all %count% elements");

    return str_replace('%count%', $count, $text);
  }

  public function getCollapseSnippet($name, $count)
  {
    if (is_null($this->getOption('collapse')) || !$this->getOption('collapse'))
    {
      return null;
    }
    
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('JavascriptBase'));
    $id = $this->generateTableId($name);

    $snippet = link_to_function(
      $this->getCollapseText($count),
      sprintf("document.getElementById('%s').style.display = (document.getElementById('%s').style.display == 'none' ? 'block' : 'none'); this.style.display = 'none';", $id, $id)
    );

    $snippet .= javascript_tag(
      sprintf("document.getElementById('%s').style.display = 'none'", $id)
    );

    return $snippet;
  }

  protected function renderHiddenInput($name, $value)
  {
    $hidden = new sfWidgetFormSelect(array('multiple' => $this->getOption('multiple'), 'is_hidden' => true, 'choices' => $this->getOption('choices')), array('style' => 'display: none'));
    return $hidden->render($name, $value);
  }
}
