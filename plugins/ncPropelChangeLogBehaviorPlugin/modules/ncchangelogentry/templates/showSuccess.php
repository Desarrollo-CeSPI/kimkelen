<?php use_helper('I18N', 'Date', 'Javascript') ?>

<div id="nc_change_log_container">
  <h1>
    <?php if (is_null($object)): ?>
      <?php echo __('Details on change log entry #%id%', array('%id%' => $nc_change_log_entry->getEntry()->getId()), 'nc_change_log_behavior') ?>
    <?php else: ?>
      <?php echo __('Details on change log entry for "%object%"', array('%object%' => $object->__toString()), 'nc_change_log_behavior') ?>
    <?php endif ?>
  </h1>

  <div id="nc_change_log_content">
    <fieldset id="nc_change_log_fieldset">
      <div class="nc_change_log_form_row">
        <div>
          <label><?php echo __('Operation type', null, 'nc_change_log_behavior') ?>:</label>
          <?php echo $nc_change_log_entry->renderOperationType() ?>
        </div>
        <div style="margin-top: 1px; clear: both"></div>
      </div>
      <div class="nc_change_log_form_row">
        <div>
          <label><?php echo __('Performed at', null, 'nc_change_log_behavior') ?>: </label>
          <?php echo $nc_change_log_entry->renderCreatedAt() ?>
        </div>
        <div style="margin-top: 1px; clear: both"></div>
      </div>
      <div class="nc_change_log_form_row">
        <div>
          <label><?php echo __('Performed by', null, 'nc_change_log_behavior') ?>:</label>
          <?php echo $nc_change_log_entry->renderUsername() ?>
        </div>
        <div style="margin-top: 1px; clear: both"></div>
      </div>
      <div class="nc_change_log_form_row">
        <div>
          <label><?php echo __('Changes made', null, 'nc_change_log_behavior') ?>:</label>
          <?php if ($nc_change_log_entry->getEntry()->isOperation(ncChangeLogEntryOperation::NC_CHANGE_LOG_ENTRY_OPERATION_UPDATE)): ?>
            <?php include_partial('show_update', array('changes' => $nc_change_log_entry)) ?>
          <?php else: ?>
            <?php echo $nc_change_log_entry->render() ?>
          <?php endif ?>
        </div>
        <div style="margin-top: 1px; clear: both"></div>
      </div>
    </fieldset>
    <?php echo javascript_tag("if (history.length > 1) { document.write('<a onclick=\"history.go(-1); return false;\" href=\"#\">".__('Go back', null, 'nc_change_log_behavior')."</a>'); }") ?>
  </div>
</div>


