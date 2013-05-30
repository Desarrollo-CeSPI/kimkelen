<?php use_helper('I18N', 'Date') ?>
<?php include_partial('license/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('New License for %person%', array("%person%"=> $person)) ?></h1>

  <?php include_partial('license/flashes') ?>

  <?php include_partial('license/form_slot_actions', array('license' => $license, 'form' => $form, 'helper' => $helper)) ?>

  <div id="sf_admin_header">
    <?php include_partial('license/form_header', array('license' => $license, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('license/form', array('license' => $license, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('license/form_footer', array('license' => $license, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>
</div>
