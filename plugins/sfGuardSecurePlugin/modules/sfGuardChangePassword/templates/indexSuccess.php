<?php use_helper('I18N','Form') ?>
<?php include_partial('sfGuardChangePassword/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Change Password for user %user%', array('%user%' => $sf_user->getUsername()), 'messages') ?></h1>

  <?php include_partial('sfGuardChangePassword/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('sfGuardChangePassword/form_header', array('form' => $form)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('sfGuardChangePassword/form', array('form' => $form)) ?>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('sfGuardChangePassword/form_footer', array('form' => $form)) ?>
  </div>
</div>
