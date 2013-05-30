<?php

/**
 * dcWidgetFormSchemaFormatterFinder
 *
 */
class dcWidgetFormSchemaFormatterFinder extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<div class=\"dc_widget_form_finder_form_row row\">\n  %error% <span class=\"dc_widget_form_finder_form_label span4\">%label%</span> <span class=\"span10\">%field%%help%</span>%hidden_fields%</div>\n",
    $errorRowFormat  = "<div>%errors%</div>\n",
    $helpFormat      = '<div class="help">%help%</div>',
    $decoratorFormat = "<div class=\"dc_widget_form_finder_form_container\">\n  %content%</div>";

}