<?php use_helper("I18N") ?>

<?php include_partial("mailer/mail_header") ?>

<h1><?php echo __("Password reset") ?></h1>

<div class="info">
	<p><strong><?php echo __("Hello, %name%", array("%name%" => ucwords($person->getFirstName()))) ?></strong></p>
	<p>Hemos recibido una solicitud de cambiar su contraseña. Para proceder con el cambio haga click en el siguiente <?php echo link_to("link", $url = url_for('@reset_password?token='.$token, true));?>
		o copie y pegue la siguiente dirección en su navegador:</p>
	<p>	<?php echo url_for($url);?></p>





  <p>Si usted no ha solicitado el cambio, por favor desestime este correo.</p>
	<p><?php echo __('Thank you') ?></p>
</div>

<?php include_partial("mailer/mail_footer") ?>