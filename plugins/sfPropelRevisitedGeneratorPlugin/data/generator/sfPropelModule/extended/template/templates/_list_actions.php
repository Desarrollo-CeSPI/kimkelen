<?php if ($actions = $this->configuration->getValue('list.actions')): ?>
<?php foreach ($actions as $name => $params): ?>
<?php if (isset($params['condition'])): ?>
[?php if (sfContext::getInstance()->getUser()-><?php echo $params['condition'] ?>()): ?]
<?php endif; ?>
<?php if ('_new' == $name): ?>
<?php echo $this->addCredentialCondition('[?php echo $helper->linkToNew('.$this->asPhp($params).') ?]', $params) ?>
<?php elseif ('_export' == $name): ?>
<?php echo $this->addCredentialCondition('[?php echo $helper->linkToExport('.$this->asPhp($params).') ?]', $params)."\n" ?>
<?php elseif ('_user_export' == $name): ?>
<?php echo $this->addCredentialCondition('[?php echo $helper->linkToUserExport('.$this->asPhp($params).') ?]', $params)."\n" ?>
<?php else: ?>
  <li class="sf_admin_action_<?php echo $params['class_suffix'] ?>">
    <?php echo $this->addCredentialCondition($this->getLinkToAction($name, $params, false), $params) ?>
  </li>
<?php endif; ?>
<?php if (isset($params['condition'])): ?>
[?php endif; ?]
<?php endif; ?>

<?php endforeach; ?>
<?php endif; ?>
