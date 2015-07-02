<?php use_helper('I18N', 'Asset', 'Tag') ?>

  <?php
    echo
    call_user_func(array($rendererClass, 'renderForm'),
      call_user_func(array($rendererClass, 'renderFormHeader'),
        call_user_func(array($rendererClass, 'renderButtonRemoveForm'), $widgetId, $childCount, image_path($images['delete-button-image']['image']), $images['delete-button-image']['text'], $afterDeleteJs),
          $formTitle),
            str_replace(array('%content%'), array($form->renderUsing($formFormatter)), $form->getWidgetSchema()->getFormFormatter()->getDecoratorFormat()),
            get_javascripts_for_form($form).get_stylesheets_for_form($form)
    );
  ?>
