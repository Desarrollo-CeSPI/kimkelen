[?php include_stylesheets_for_form($form) ?]
[?php include_javascripts_for_form($form) ?]

<div class="sf_admin_form">
    [?php include_partial('<?php echo $this->getModuleName() ?>/show_form_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?]
    <?php $show_displays = $this->configuration->getValue('show.display'); ?>
    <?php if(!empty($show_displays)): ?>
      <?php foreach ($show_displays as $category => $fields): ?>
        <fieldset id="sf_fieldset_[?php echo preg_replace('/[^a-z0-9_]/', '_', '<?php echo strtolower($category) ?>') ?]">
        <?php if ($category != 'NONE'): ?>
          <h2>[?php echo __('<?php echo $category ?>') ?]</h2>
        <?php endif; ?>
        <?php foreach ($fields as $name => $field): ?>
          [?php slot('sf_admin.current_show_<?php echo $name?>') ?]
          <?php if(!$field->isPartial() && !$field->isComponent()): ?>
            <?php $class = 'sf_admin_form_row sf_admin_'.$field->getType().' sf_admin_form_field_'.$name ?>
             <div class="<?php echo $class ?>">
              <div>
                <label for="<?php echo $name ?>">
                  [?php echo __('<?php echo $field->getConfig('label', '', true); ?>', array(), '<?php echo $this->getI18nCatalogue()?>') ?]
                </label>
                <?php $rendered_field = $this->renderField($field) ?>
                [?php $rendered_field = <?php echo $this->renderField($field) ?>; ?]
                [?php echo (($rendered_field instanceof sfOutputEscaper) ? $rendered_field->getRawValue() : $rendered_field) ?]
              </div>
              <div style="margin-top: 1px; clear: both"></div>
            </div>
          <?php else: ?>
            [?php echo <?php echo $this->renderField($field)?> ?]
          <?php endif; ?>
          [?php end_slot(); ?]
          <?php echo $this->addCredentialCondition("[?php include_slot('sf_admin.current_show_$name') ?]", $field->getConfig()) ?>
        <?php endforeach; ?>
        </fieldset>
      <?php endforeach; ?>
    <?php else: ?>
      [?php foreach ($configuration->getFormFields($form, 'show') as $fieldset => $fields): ?]
        [?php include_partial('<?php echo $this->getModuleName() ?>/show_form_fieldset', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?]
      [?php endforeach; ?]
    <?php endif; ?>
    [?php include_partial('<?php echo $this->getModuleName() ?>/show_form_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?]
</div>

