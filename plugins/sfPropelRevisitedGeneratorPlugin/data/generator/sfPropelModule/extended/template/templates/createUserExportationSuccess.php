<?php if ($this->configuration->isExportationEnabled()): ?>
[?php include_partial('<?php echo $this->getModuleName()?>/exportation_pages', array('pager' => $pager, 'exportUrl' => 'processUserExportation')) ?]
<?php endif ?>
