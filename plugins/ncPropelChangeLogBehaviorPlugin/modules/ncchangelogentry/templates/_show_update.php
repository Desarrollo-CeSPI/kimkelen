<?php if (count($changes) > 0): ?>
  <table>
    <thead>
      <tr>
        <th><label><?php echo __('Field', array(), 'nc_change_log_behavior') ?></label></th>
        <th><label><?php echo __('Old value', array(), 'nc_change_log_behavior') ?></label></th>
        <th><label><?php echo __('New value', array(), 'nc_change_log_behavior') ?></label></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th colspan="3">
          <label><?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => count($changes)), count($changes), 'nc_change_log_behavior') ?></label>
        </th>
      </tr>
    </tfoot>
    <tbody>
      <?php foreach ($changes as $field => $change): ?>
        <tr>
          <td><?php echo $change->renderFieldName() ?></td>
          <td>
            <?php if (ncChangeLogConfigHandler::shouldEscapeValues()): ?>
              <?php echo sfOutputEscaper::unescape($change->getOldValue(true)) ?>
            <?php else: ?>
              <?php echo $change->getOldValue(true) ?>
            <?php endif ?>
          </td>
          <td>
            <?php if (ncChangeLogConfigHandler::shouldEscapeValues()): ?>
              <?php echo sfOutputEscaper::unescape($change->getNewValue(true)) ?>
            <?php else: ?>
              <?php echo $change->getNewValue(true) ?>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
<?php endif ?>

