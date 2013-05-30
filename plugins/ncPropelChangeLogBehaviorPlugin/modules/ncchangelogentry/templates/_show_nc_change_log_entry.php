  <fieldset id="nc_change_log_fieldset">
    <div class="nc_change_log_form_row nc_change_log_form_field">
      <div>
        <label><?php echo __('Class', null, 'nc_change_log_behavior') ?>:</label>
        <?php echo $nc_change_log_entry->renderClassName() ?>
      </div>
      <div style="margin-top: 1px; clear: both"></div>
    </div>

    <div class="nc_change_log_form_row nc_change_log_form_field">
      <div>
        <label><?php echo __('Operation type', null, 'nc_change_log_behavior') ?>:</label>
        <?php echo $nc_change_log_entry->renderOperationType() ?>
      </div>
      <div style="margin-top: 1px; clear: both"></div>
    </div>
    <div class="nc_change_log_form_row nc_change_log_form_field">
      <div>
        <label><?php echo __('Performed at', null, 'nc_change_log_behavior') ?>: </label>
        <?php echo $nc_change_log_entry->renderCreatedAt() ?>
      </div>
      <div style="margin-top: 1px; clear: both"></div>
    </div>
    <div class="nc_change_log_form_row nc_change_log_form_field">
      <div>
        <label><?php echo __('Performed by', null, 'nc_change_log_behavior') ?>:</label>
        <?php echo $nc_change_log_entry->renderUsername() ?>
      </div>
      <div style="margin-top: 1px; clear: both"></div>
    </div>
    <div class="nc_change_log_form_row nc_change_log_form_field">
      <div>
        <label><?php echo __('Changes made', null, 'nc_change_log_behavior') ?>:</label>
        <?php if ($nc_change_log_entry->isOperation(ncChangeLogEntryOperation::NC_CHANGE_LOG_ENTRY_OPERATION_UPDATE)): ?>
          <?php include_partial('show_update', array('nc_change_log_entry' => $nc_change_log_entry)) ?>
        <?php else: ?>
          <?php echo $nc_change_log_entry->render() ?>
        <?php endif ?>
      </div>
      <div style="margin-top: 1px; clear: both"></div>
    </div>
  </fieldset>
