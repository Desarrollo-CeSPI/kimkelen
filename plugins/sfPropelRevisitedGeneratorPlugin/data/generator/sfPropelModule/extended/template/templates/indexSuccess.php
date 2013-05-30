[?php use_helper('I18N', 'Date', 'Javascript') ?]
[?php include_partial('<?php echo $this->getModuleName() ?>/assets') ?]

<div id="sf_admin_container">
  <?php if ($this->configuration->isExportationEnabled()): ?>
    [?php include_partial('exportation', array('configuration' => $configuration)) ?]
  <?php endif ?>

  <h1>[?php echo <?php echo $this->getI18NString('list.title') ?> ?]</h1>

  [?php include_partial('<?php echo $this->getModuleName() ?>/list_slot_actions', array('helper' => $helper)) ?]

  <div id="sf_admin_header">
    [?php include_partial('<?php echo $this->getModuleName() ?>/list_header', array('pager' => $pager)) ?]
  </div>

<?php //if ($this->configuration->hasFilterForm()): ?>
  [?php if ($configuration->hasFilterForm()): ?]
  <div align="center">
    <div id="sf_admin_bar">
      [?php include_partial('<?php echo $this->getModuleName() ?>/filters', array('form' => $filters, 'configuration' => $configuration)) ?]
    </div>
  </div>
  [?php endif ?]
<?php //endif; ?>

  [?php include_partial('<?php echo $this->getModuleName() ?>/flashes') ?]

  <div id="sf_admin_content">
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    <form action="[?php echo url_for('<?php echo $this->getModuleName() ?>/allBatch', array('action' => 'batch')) ?]" method="post">
<?php endif; ?>
    <ul class="sf_admin_actions">
      [?php include_partial('<?php echo $this->getModuleName() ?>/list_all_batch_actions', array('helper' => $helper)) ?]
    </ul>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    </form>
<?php endif; ?>

<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    <form action="[?php echo url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'batch')) ?]" method="post">
<?php endif; ?>
    <ul class="sf_admin_actions">
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
      <input type="hidden" id="batch_action" name="batch_action">
<?php endif; ?>
      [?php include_partial('<?php echo $this->getModuleName() ?>/list_batch_actions', array('helper' => $helper, 'select_id' => 'top')) ?]
      [?php include_partial('<?php echo $this->getModuleName() ?>/list_actions', array('helper' => $helper)) ?]
    </ul>
    [?php include_partial('<?php echo $this->getModuleName() ?>/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?]
    <ul class="sf_admin_actions">
      [?php include_partial('<?php echo $this->getModuleName() ?>/list_batch_actions', array('helper' => $helper, 'select_id' => 'bottom')) ?]
      [?php include_partial('<?php echo $this->getModuleName() ?>/list_actions', array('helper' => $helper)) ?]
    </ul>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    </form>
<?php endif; ?>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    <form action="[?php echo url_for('<?php echo $this->getModuleName() ?>/allBatch', array('action' => 'batch')) ?]" method="post">
<?php endif; ?>
    <ul class="sf_admin_actions">
      [?php include_partial('<?php echo $this->getModuleName() ?>/list_all_batch_actions', array('helper' => $helper)) ?]
    </ul>
<?php if ($this->configuration->getValue('list.batch_actions')): ?>
    </form>
<?php endif; ?>
  </div>

  <div id="sf_admin_footer">
    [?php include_partial('<?php echo $this->getModuleName() ?>/list_footer', array('pager' => $pager)) ?]
  </div>
</div>
[?php if ($configuration->isExportationEnabled()): ?>
  [?php javascript_tag() ?]
    jQuery(window).bind('resize', function() {
      jQuery('#sf_admin_exportation').centerHorizontally();
      jQuery('#sf_admin_exportation_resizable_area').ensureVisibleHeight();
    });
  [?php end_javascript_tag() ?]
[?php endif ?]
