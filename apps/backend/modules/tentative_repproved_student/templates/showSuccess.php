<?php use_helper('Javascript','I18N','Form', 'Asset') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
	<h1><?php echo __('Resolver alumnos problemáticos') ?></h1>
	<h2><?php echo __('El siguiente listado representa aquellos alumnos que superaron el límite de materias previas estableciodo por
	la institución. Seleccionelos para inscribirlos en el programa de Trayectorias Inteligentes/Promoción por excepción. Si no los selecciona los mismos repetirán el año lectivo actual.') ?></h2>

	<div id="sf_admin_content">
		<form action="<?php echo url_for('tentative_repproved_student/save') ?>" method="POST">
			<ul class="sf_admin_actions">
				<li><?php echo link_to(__('Back'), 'schoolyear/index', array('class' => 'sf_admin_action_go_back')) ?></li>
				<li><input type="submit" value="<?php echo __('Save') ?>" /></li>
			</ul>
			<fieldset>
				<?php echo $form ?>
			</fieldset>

			<ul class="sf_admin_actions">
				<li><?php echo link_to(__('Back'), 'schoolyear/index', array('class' => 'sf_admin_action_go_back')) ?></li>
				<li><input type="submit" value="<?php echo __('Save') ?>" /></li>
			</ul>
		</form>
	</div>
	<div style="margin-top: 1px; clear: both;">
	</div>
</div>