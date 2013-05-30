<?php if ($listActions = $this->configuration->getValue('list.batch_actions')): ?>
<li class="sf_admin_all_batch_actions_choice">
  <select id="all_batch_action" name="all_batch_action">
    <option value="">[?php echo __('Choose an action to be applied to all results', array(), 'sf_admin') ?]</option>
<?php foreach ((array) $listActions as $action => $params): ?>
    <?php echo $this->addCredentialCondition('<option value="'.$action.'">[?php echo __(\''.$params['label'].'\', array(), \'sf_admin\') ?]</option>', $params) ?>
<?php endforeach; ?>
  </select>
  [?php $form = new sfForm(); if ($form->isCSRFProtected()): ?]
    <input type="hidden" name="[?php echo $form->getCSRFFieldName() ?]" value="[?php echo $form->getCSRFToken() ?]" />
  [?php endif; ?]
  <input type="submit" value="[?php echo __('go', array(), 'sf_admin') ?]" onclick="confirm('[?php echo __("This could take a while. Are you sure?", array(), 'sf_admin') ?]')"/>
</li>
<?php endif; ?>
