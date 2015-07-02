<?php if ($this->configuration->getValue('list.object_actions')): ?>
<td>
  <ul class="sf_admin_td_actions">
  [?php $disabled_actions=array(); ?]
<?php foreach ($this->configuration->getValue('list.object_actions') as $name => $params): ?>
<?php if (isset($params['condition'])): ?>
  [?php if ($<?php echo $this->getSingularName() ?>-><?php echo $params['condition'] ?>()): ?]
<?php endif; ?>
<?php if ('_delete' == $name): ?>
  <?php echo $this->addCredentialCondition('[?php echo $helper->linkToDelete($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params) ?>
<?php elseif ('_edit' == $name): ?>
  <?php echo $this->addCredentialCondition('[?php echo $helper->linkToEdit($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params) ?>
<?php elseif ('_show' == $name): ?>
  <?php echo $this->addCredentialCondition('[?php echo $helper->linkToShow($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params) ?>
<?php else: ?>
  <?php echo $this->addCredentialCondition($this->getLinkToAction($name, $params, true), $params) ?>
<?php endif; ?>
<?php if (isset( $params['condition'])): ?>
  [?php else: ?]
    <?php echo $this->addCredentialCondition('[?php $disabled_actions[]= $helper->getDisabledAction($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params); ?>
  [?php endif; ?]
<?php endif; ?>
<?php endforeach; ?>
  </ul>
  [?php echo $helper->getDisabledActions($disabled_actions); ?]
</td>
<?php endif; ?>

