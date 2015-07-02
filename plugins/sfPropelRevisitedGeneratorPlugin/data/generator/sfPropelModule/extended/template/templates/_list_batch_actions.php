<?php if ($listActions = $this->configuration->getValue('list.batch_actions')): ?>
<li class="sf_admin_batch_actions_choice">
  <select id="[?php echo isset($select_id)?$select_id.'_':'' ?]batch_action" name="[?php echo isset($select_id)?$select_id.'_':'' ?]batch_action" onchange="$('batch_action').value = $('[?php echo isset($select_id)?$select_id.'_':'' ?]batch_action').options[$('[?php echo isset($select_id)?$select_id.'_':'' ?]batch_action').selectedIndex].value;">
    <option value="">[?php echo __('Choose an action', array(), 'sf_admin') ?]</option>
<?php foreach ((array) $listActions as $action => $params): ?>
    <?php echo $this->addCredentialCondition('<option value="'.$action.'">[?php echo __(\''.$params['label'].'\', array(), \'sf_admin\') ?]</option>', $params) ?>

<?php endforeach; ?>
  </select>
  [?php $form = new sfForm(); if ($form->isCSRFProtected()): ?]
    <input type="hidden" name="[?php echo $form->getCSRFFieldName() ?]" value="[?php echo $form->getCSRFToken() ?]" />
  [?php endif; ?]
  <input type="submit" name="_[?php echo (isset($select_id)?$select_id.'_':'').'batch_action' ?]" value="[?php echo __('go', array(), 'sf_admin') ?]" onclick="if (!$('[?php echo isset($select_id)?$select_id.'_':'' ?]batch_action').getValue()) return false;"  />
</li>
<?php endif; ?>
