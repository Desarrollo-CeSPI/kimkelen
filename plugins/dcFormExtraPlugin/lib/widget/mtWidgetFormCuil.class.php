<?php


/*
 * DEBE DE SER USADOC CON EL VALIDADOR mtValidatorCuilExtended !!!
 */
class mtWidgetFormCuil extends sfWidgetForm
{
  protected
    $decoratorString = "<div class=\"mtWidgetFormCuil\">
  %prefix%%separator%%middle%%separator%%suffix%
</div>";

  public function configure($options = array(), $attributes = array())
  {
    parent::configure($options,$attributes);

    $this->addOption('prefix_widget', new sfWidgetFormInput(array(), array('class' => 'mtWidgetFormCuilPrefix')));
    $this->addOption('middle_widget', new sfWidgetFormInput(array(), array('class' => 'mtWidgetFormCuilMiddle')));
    $this->addOption('suffix_widget', new sfWidgetFormInput(array(), array('class' => 'mtWidgetFormCuilSuffix')));
    $this->addOption('separator', '-');
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $value = $this->parseValue($value);

    return str_replace(
      array('%prefix%', '%middle%', '%suffix%', '%separator%'),
      array(
        $this->getOption('prefix_widget')->render($name.'[prefix]', $value['prefix'], $attributes, $errors),
        $this->getOption('middle_widget')->render($name.'[middle]', $value['middle'], $attributes, $errors),
        $this->getOption('suffix_widget')->render($name.'[suffix]', $value['suffix'], $attributes, $errors),
        $this->getOption('separator')
      ),
      $this->decoratorString
    );
  }

  protected function parseValue($value)
  {
    if (!empty($value) && !is_array($value))
    {
      $value = explode('-', CuitFormatter::format($value));
      if (count($value) == 3)
      {
        $value = array(
          'prefix' => $value[0],
          'middle' => $value[1],
          'suffix' => $value[2]
        );
      }
    }
    elseif (!is_array($value)
            && (
              !isset($value['prefix'])
               || !isset($value['middle'])
               || !isset($value['suffix'])
               )
           )
    {
      $value = array(
        'prefix' => '',
        'middle' => '',
        'suffix' => '',
      );
    }

    return $value;
  }
}
