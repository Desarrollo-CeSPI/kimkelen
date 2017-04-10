<h1> <?php echo ('Usuario de acceso al sistema') ?></h1>

<h3>A través del siguiente nombre de usuario y contraseña podrá ingresar al sistema. Se le solicitará un cambio de contraseña obligatorio al ingresar por primera vez.</h3>
<div id="nota">
	<p><?php echo __('Username') ?>: <strong><?php echo $user->getUsername(); ?></strong></p>
	<p><?php echo __('Password') ?>: <strong><?php echo $password; ?></strong></p>
</div>

<div>
	<p> Ingrese al sistema desde el siguiente link:  <strong><?php echo $link ?></strong></p>
</div>
