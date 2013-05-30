[?php if ($form->isNew()): ?]
  [?php slot('<?php echo $this->configuration->getNewSlotName() ?>') ?]
  <?php $actions = $this->configuration->getNewSlotActions() ?>
  <?php foreach ($actions as $name => $params): ?>
    <?php if ('_new' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToNew('.$this->asPhp($params).') ?]', $params) ?>

    <?php elseif ('_list' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToList('.$this->asPhp($params).') ?]', $params) ?>

    <?php elseif ('_save' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToSave($form->getObject(), '.$this->asPhp($params).') ?]', $params) ?>

    <?php elseif ('_save_and_add' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToSaveAndAdd($form->getObject(), '.$this->asPhp($params).') ?]', $params) ?>
    
    <?php elseif ('_save_and_list' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToSaveAndList($form->getObject(), '.$this->asPhp($params).') ?]', $params) ?>

    <?php else: ?>
      <?php echo $this->addCredentialCondition($this->getSlotAction($name, $params, true), $params) ?>
    <?php endif; ?>
  <?php endforeach; ?>
  [?php end_slot() ?]
[?php else: ?]
  [?php slot('<?php echo $this->configuration->getEditSlotName() ?>') ?]
  <?php $actions = $this->configuration->getEditSlotActions() ?>
  <?php foreach ($actions as $name => $params): ?>
    <?php if ('_new' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToNew('.$this->asPhp($params).') ?]', $params) ?>

    <?php elseif ('_delete' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToDelete($form->getObject(), '.$this->asPhp($params).') ?]', $params) ?>

    <?php elseif ('_list' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToList('.$this->asPhp($params).') ?]', $params) ?>

    <?php elseif ('_save' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToSave($form->getObject(), '.$this->asPhp($params).') ?]', $params) ?>

    <?php elseif ('_save_and_add' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToSaveAndAdd($form->getObject(), '.$this->asPhp($params).') ?]', $params) ?>
    
    <?php elseif ('_save_and_list' == $name): ?>
      <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToSaveAndList($form->getObject(), '.$this->asPhp($params).') ?]', $params) ?>
      
    <?php else: ?>
      <?php echo $this->addCredentialCondition($this->getSlotAction($name, $params, true), $params) ?>
    <?php endif; ?>
  <?php endforeach; ?>
  [?php end_slot() ?]
[?php endif ?]
