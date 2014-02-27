<?php use_helper('I18N', 'Date') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php include_partial('commission/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('New subject for commission') ?></h1>

  <?php include_partial('commission/flashes') ?>

  <div id="sf_admin_header">
  </div>

  <div id="sf_admin_content">
    <div class="sf_admin_form">
      <form action="<?php echo url_for('commission/addSubject') ?>" method="post" >

        <?php echo $form->renderHiddenFields() ?>

        <?php if ($form->hasGlobalErrors()): ?>
          <?php echo $form->renderGlobalErrors() ?>
        <?php endif; ?>

        <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
          <fieldset id="sf_fieldset_<?php echo preg_replace('/[^a-z0-9_]/', '_', strtolower($fieldset)) ?>">
            <?php if ('NONE' != $fieldset): ?>
              <h2><?php echo __($fieldset, array(), 'messages') ?></h2>
            <?php endif; ?>

            <?php foreach ($fields as $name => $field): ?>
              <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
              <?php
              include_partial('commission/form_field', array(
                'name' => $name,
                'attributes' => $field->getConfig('attributes', array()),
                'label' => $field->getConfig('label'),
                'help' => $field->getConfig('help'),
                'form' => $form,
                'field' => $field,
                'class' => 'sf_admin_form_row sf_admin_' . strtolower($field->getType()) . ' sf_admin_form_field_' . $name,
              ))
              ?>
            <?php endforeach; ?>
          </fieldset>
        <?php endforeach; ?>
        <ul class="sf_admin_actions">

          <li class ="sf_admin_action_list"><?php echo link_to(__('Back'), 'commission'); ?></li>
          <li class ="sf_admin_action_list"><input type="submit" value="<?php echo __('Save', array(), 'sf_admin') ?>" /></li>
        </ul>

      </form>
    </div>
  </div>
</div>