<?php $actions = $this->configuration->getListSlotActions() ?>
[?php slot('<?php echo $this->configuration->getListSlotName() ?>') ?]
<?php foreach ($actions as $name => $params): ?>
  <?php if ('_new' == $name): ?>
    <?php echo $this->addCredentialCondition('[?php echo $helper->slotActionToNew('.$this->asPhp($params).') ?]', $params) ?>
  <?php else: ?>
    <?php echo $this->addCredentialCondition($this->getSlotAction($name, $params, false), $params) ?>
  <?php endif; ?>
<?php endforeach; ?>
[?php end_slot() ?]