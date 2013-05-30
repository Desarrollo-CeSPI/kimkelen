<?php

/**
 * dcWidgetEmbeddedFormSchemaFormatter
 * 
 * sfWidgetFormSchemaFormatter subclass suitable for embedded forms in
 * an sfAdminGenerator context.
 *
 * @author ncuesta
 */
class dcWidgetEmbeddedFormSchemaFormatter extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat                 = "%hidden_fields%\n<div class=\"sf_admin_form_row sf_admin_form_embedded_row\">\n  %label%  <div>\n    %error%%field%  </div>\n</div>",
    $helpFormat                = '<div class="help">%help%</div>',
    $decoratorFormat           = "<div class=\"sf_admin_embedded_form\">\n %content%</div>";
}