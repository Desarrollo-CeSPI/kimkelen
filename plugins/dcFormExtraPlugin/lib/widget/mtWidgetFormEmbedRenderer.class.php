<?php

class mtWidgetFormEmbedRenderer
{
  static public $decoratorString      = '<div id=wrapper_%id% class="mtWidgetFormEmbed">
  %title%
  %toolbar%
  <div class="mtWidgetFormEmbedWorkspace">
    %embedded_forms%
  </div>
  <div class="mtWidgetFormEmbedSelectTag">
    %hidden_tags%
  </div>
</div>';

  static public $decoratorHeaderString = '<div class="mtWidgetFormEmbedFormTitle">
  <div class="mtWidgetFormEmbedFormTitleActions">
    %actions%
  </div>
  <h2>%title%</h2>
</div>';

  static public $decoratorTitleString = '<h1 class="mtWidgetFormEmbedTitle">%title%</h1>';

  static public $decoratorFormActionString = '<a href="#" onclick="%js_function%"><img src="%img_src%" /><span class="action-text">%text%</span></a>';

  static public $decoratorToolbarString = '<div class="mtWidgetFormEmbedToolbar">
  %toolbar_buttons%
</div>';

  static public $decoratorToolbarButtonString  = '<a href="#" onclick="%js_function%">
  <span class="mtWidgetFormEmbedToolbarButton">
    <span class="image"><img alt="%img_text%" src="%img_src%" /></span>
    <span class="text">%img_text%</span>
  </span>
</a>';

   static public $decoratorFormString = '<div class="mtWidgetFormEmbedForm">
  %header%
  %hidden_fields%
  %global_errors%
  %form%
</div>';

  static public function renderForm($formHeaderHtml, $formHtml, $globalErrorsHtml = '', $hiddenFieldsHtml = '')
  {
    return str_replace(
      array('%header%', '%form%', '%hidden_fields%', '%global_errors%'),
      array($formHeaderHtml, $formHtml, $hiddenFieldsHtml, $globalErrorsHtml),
      self::$decoratorFormString);
  }

  static public function renderFormHeader($actions, $formTitle)
  {
    return str_replace(
        array('%actions%', '%title%'),
        array($actions, $formTitle),
        self::$decoratorHeaderString
      );
  }

  static public function renderFormAction($id, $formIndex, $imgSrc, $text, $js)
  {
    return str_replace(
      array('%id%', '%form_index%', '%img_src%', '%js_function%', '%text%'),
      array($id, $formIndex, $imgSrc, $js, $text),
      self::$decoratorFormActionString
    );
  }

  static public function renderButtonRemoveForm($id, $formIndex, $imgSrc, $text)
  {
    $js = str_replace(
      array('%id%', '%form_index%'),
      array($id, $formIndex),
      "jQuery('#%id% option[value=%form_index%]').remove(); jQuery(this).parents('.mtWidgetFormEmbedForm').remove();"
    );

    return mtWidgetFormEmbedRenderer::renderFormAction($id, $formIndex, $imgSrc, $text, $js);
  }

  static public function renderToolbarButton($js, $text, $image)
  {
    return str_replace(
      array('%js_function%', '%img_text%', '%img_src%'),
      array($js, $text, $image),
      self::$decoratorToolbarButtonString
    );
  }

  static public function renderToolbar($name, $buttons)
  {
    return str_replace(
      array('%toolbar_buttons%'),
      array($buttons),
      self::$decoratorToolbarString
    );
  }

  static public function render($id, $toolbar, $forms, $hiddenTags, $title = '')
  {
    return str_replace(
      array('%id%', '%toolbar%', '%embedded_forms%', '%hidden_tags%', '%title%'),
      array($id, $toolbar, $forms, $hiddenTags, $title),
      self::$decoratorString
    );
  }

  static public function renderTitle($title)
  {
    if (!empty($title))
    {
      return str_replace(
        array('%title%'),
        array($title),
        self::$decoratorTitleString
      );
    }
    return '';
  }
}
