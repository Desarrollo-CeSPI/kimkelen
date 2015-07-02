<?php

/**
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormSchemaFormatterTable.class.php 5995 2007-11-13 15:50:03Z fabien $
 */
class sfWidgetFormSchemaFormatterRevisited extends sfWidgetFormSchemaFormatter
{
  protected
    $form            = null,
    $rowFormat       = "<div class=\"sf_admin_form_row\">\n
                          %error%\n
                          %label%\n
                          %field%\n
                          <div class=\"help\">\n
                            %help%\n
                          </div>\n%hidden_fields%\n
                          <div style=\"clear:both;margin-top:1px;\"></div>
                        </div>\n",
    $errorRowFormat  = "<div class=\"errors\">\n%errors%</div>\n",
    $helpFormat      = '%help%',
    $decoratorFormat = "<div>\n  %content%</div>";

  public function __construct(sfForm $form)
  {
    parent::__construct($form->getWidgetSchema());
    $this->setForm($form);
  }

  public function setForm($form)
  {
    $this->form = $form;
  }

  public function getForm()
  {
    return $this->form;
  }

  public function generateLabel($name, $attributes = array())
  {
    $validatorSchema = $this->form->getValidatorSchema();
    $class = (isset($validatorSchema[$name]) && $validatorSchema[$name]->getOption('required')) ? 'required' : '';
    if (isset($attributes['class']))
    {
      $attributes['class'] .= ' '.$class;
    }
    else
    {
      $attributes['class'] = $class;
    }
    return parent::generateLabel($name, $attributes);
  }
}