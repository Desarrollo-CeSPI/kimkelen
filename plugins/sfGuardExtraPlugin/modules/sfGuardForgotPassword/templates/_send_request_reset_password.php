<?php use_helper('I18N') ?>
<?php echo __('Hello') ?> <?php echo $sfGuardUser->getUsername() ?>,

<?php echo __('Click here to have your password reset and mailed to you') ?>

<?php echo url_for('@sf_guard_forgot_password_reset_password?key='.$sfGuardUser->getPassword().'&id='.$sfGuardUser->getId(), true) ?>
