<?php use_helper('I18N', 'Asset', 'Tag') ?>

<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

  <?php
    echo
    call_user_func(array($rendererClass, 'renderForm'),
      call_user_func(array($rendererClass, 'renderFormHeader'),
        call_user_func(array($rendererClass, 'renderButtonRemoveForm'), $widgetId, $childCount, image_path($images['delete-button-image']['image']), $images['delete-button-image']['text']),
          $formTitle),
            str_replace(array('%content%'), array($form->renderUsing($formFormatter)), $form->getWidgetSchema()->getFormFormatter()->getDecoratorFormat())
    );
  ?>
