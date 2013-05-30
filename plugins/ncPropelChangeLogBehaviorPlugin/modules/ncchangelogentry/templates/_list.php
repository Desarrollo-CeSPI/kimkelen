  <div <?php !is_null($id) and print 'style="display: none"' ?> class="nc_change_log_list" id="<?php echo $id?>">
    <?php if (empty($nc_change_log_entries)): ?>
      <p><?php echo __('No entries have been found for this object', null, 'nc_change_log_behavior') ?></p>
    <?php else: ?>
      <table cellspacing="0">
        <thead>
          <tr>
            <th><?php echo __('Operation type', null, 'nc_change_log_behavior') ?></th>
            <th><?php echo __('Performed at', null, 'nc_change_log_behavior') ?></th>
            <th id="nc_change_log_list_th_actions"><?php echo __('Actions', array(), 'nc_change_log_behavior') ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th colspan="3">
              <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => count($nc_change_log_entries)), count($nc_change_log_entries), 'nc_change_log_behavior') ?>
            </th>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach ($nc_change_log_entries as $i => $nc_change_log_entry): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?>
            <tr class="nc_change_log_row <?php echo $odd ?>">
              <td><?php echo $nc_change_log_entry->renderOperationType() ?></td>
              <td><?php echo $nc_change_log_entry->renderCreatedAt() ?></td>
              <td class="nc_change_log_list_td_actions"><?php echo link_to(__('Show details', null, 'nc_change_log_behavior'), '@nc_change_log_detail?id='.$nc_change_log_entry->getEntry()->getId()) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
