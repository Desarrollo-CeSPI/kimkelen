<?php use_helper('I18N') ?>
<?php echo __('The link you followed is not valid.
If you wish to have your password reset please %link% and fill out the form
and a link to reset your password will be provided.
Once your password is reset it will be e-mailed to you.',
  array('%link%' => link_to(__('go here'), '@sf_guard_password'))) ?>