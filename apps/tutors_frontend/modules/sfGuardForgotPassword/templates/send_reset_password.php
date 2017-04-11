<?php use_helper('I18N') ?>
<?php echo __('Your password has been reset, your new login information can be found below') ?>:

<?php echo __('Username') ?>: <?php echo $sfGuardUser->getUsername() ?>

<?php echo __('Password') ?>: <?php echo $password ?>
