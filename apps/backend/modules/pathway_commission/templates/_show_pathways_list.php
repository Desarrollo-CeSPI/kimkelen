<div class="sf_admin_form_row">
  <div>
    <label for="pathways_list"><?php echo __('Trayectorias de la comisión'); ?></label>
  </div>
  <?php include_partial('pathway_commission/pathways_list', array('course'=>$course, 'id' => 'pathways_list') ) ?>
  <div style="margin-top: 1px; clear: both"></div>
</div>
