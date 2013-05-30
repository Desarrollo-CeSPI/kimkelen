[?php use_helper('I18N', 'Date', 'Javascript') ?]
<?php if ($this->configuration->isExportationEnabled()): ?>
<fieldset>
  [?php if ($pager->getNbResults() > 0): ?]
    <h2>[?php echo __('Please, select the group of items you want to export', array(), 'sf_admin') ?]</h2>
    <div id="sf_admin_exportation_resizable_area">
      <div class="print-helper">
        <ul class="print_label_actions">
        [?php foreach ($pager->getLinks($pager->getNbResults()/$pager->getMaxPerPage()) as $page): ?]
          <li class="sf_admin_action_export_label">
            [?php echo link_to(__('Export results from %1% to %2%',
                                  array('%1%' => ($page-1)*$pager->getMaxPerPage()+1, '%2%' => min($pager->getNbResults(), ($page*$pager->getMaxPerPage()))),
                                  'sf_admin'),
                               "<?php echo $this->getModuleName()?>/$exportUrl?page=$page") ?]
          </li>
        [?php endforeach; ?]
        </ul>
      </div>
    </div>
  [?php else: ?]
    <h2>[?php echo __('No results', array(), 'sf_admin') ?]</h2>
    <div id="sf_admin_exportation_resizable_area">
      <ul class="print_label_actions">
        <li class="sf_admin_action_export_label">
          [?php echo __('There were no results with the current search options.', array(), 'sf_admin')  ?]
        </li>
      </ul>
    <div>
  [?php endif ?]
</fieldset>
<div class="actions">
  <ul class="sf_admin_exportation_actions">
    <li class="sf_admin_action_list">[?php echo link_to_function(__('Cancel', array(), 'sf_admin'), "jQuery('#sf_admin_exportation').hide()") ?]</li>
  </ul>
</div>
<?php endif ?>
