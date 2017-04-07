<?php use_helper('I18N') ?>
<?php echo __('Hello') ?> <?php echo $sfGuardUser->getUsername() ?>,

<?php echo __('Click here to confirm your registration') ?>

<?php echo url_for('@sf_guard_register_confirm?key='.$sfGuardUser->getPassword().'&id='.$sfGuardUser->getId(), true) ?>


<?php echo __('Your login information can be found below') ?>:

<?php echo __('Username') ?>: <?php echo $sfGuardUser->getUsername() ?>

<?php echo __('Password') ?>: <?php echo $password ?>