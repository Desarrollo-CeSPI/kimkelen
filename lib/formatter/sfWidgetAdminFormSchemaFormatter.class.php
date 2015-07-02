<?php

/**
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormSchemaFormatterTable.class.php 5995 2007-11-13 15:50:03Z fabien $
 */
class sfWidgetAdminFormSchemaFormatter extends sfWidgetFormSchemaFormatter
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
                        </div>\n",
    $errorRowFormat  = "<div class=\"errors\">\n%errors%</div>\n",
    $helpFormat      = '%help%',
    $decoratorFormat = "<div>\n  %content%</div>";
}