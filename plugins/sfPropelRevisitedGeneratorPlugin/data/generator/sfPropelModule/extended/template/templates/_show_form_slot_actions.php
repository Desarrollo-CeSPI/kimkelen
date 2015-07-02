[?php slot('<?php echo $this->configuration->getShowSlotName() ?>') ?]
  <?php $actions = $this->configuration->getShowSlotActions() ?>
  <?php foreach ($actions as $name => $params): ?>
    <?php if ('_delete' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToDelete($form->getObject(), '.$this->asPhp($params).') ?]', $params) ?>

    <?php elseif ('_list' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToList('.$this->asPhp($params).') ?]', $params) ?>

    <?php elseif ('_edit' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToEdit($form->getObject(), '.$this->asPhp($params).') ?]', $params) ?>

    <?php else: ?>
      <?php echo $this->addCredentialCondition($this->getSlotAction($name, $params, true), $params) ?>
    <?php endif; ?>
  <?php endforeach; ?>
[?php end_slot() ?]
