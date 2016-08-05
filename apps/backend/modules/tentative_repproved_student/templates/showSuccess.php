<?php use_helper('Javascript','I18N','Form', 'Asset') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
	<h1><?php echo __('Resolve problematic students') ?></h1>
	<h3><?php echo __('Create pathway if it does not exist from') ?> <span class="yellow_link"><a target="_blank" href="<?php echo url_for('pathway') ?>"><?php echo __("here") ?></a></span></h3>

	<div id="pathway-list">
		<h2><?php echo __('Alumnos inscriptos en trayectoria:') ?></h2>
		
		<?php if (!empty($students)): ?>
			<table>
				<?php foreach ($students as $student):?>
					<tr>
						<th><?php echo $student ?></th>
						<td><?php echo link_to(__('Delete'), "tentative_repproved_student/deleteStudent?id=" . $student->getId() . "&student_id=" . $student->getId())?></td>
					</tr>
				<?php endforeach?>
			</table>
		<?php else: ?>
			<p style="margin-left: 1%"><?php echo 'Aún no hay alumnos inscriptos.' ?></p>
		<?php endif; ?>
		<a href="<?php echo url_for('tentative_repproved_student/finish') ?>" class="warning-button" onclick="return (confirm('¿Está seguro? Los alumnos que no fueron seleccionados repetirán el año lectivo actual.'));"><?php echo __('Finalizar') ?></a>
	</div>

	<ul>
		<li style="list-style: disc !important"><h2><?php echo __("El siguiente listado representa aquellos alumnos que superaron el límite de materias previas establecido por la institución.") ?></h2></li>
		<li style="list-style: disc !important"><h2><?php echo __("Para inscribirlos en el programa de Trayectorias seleccionelos y haga click en 'Guardar'.") ?></h2></li>
		<li style="list-style: disc !important; text-decoration: underline;"><h2><?php echo __("Todo alumno NO seleccionado repetirá el año lectivo actual al hacer click en 'Finalizar'.") ?></h2></li>
	</ul>

	<div id="pathway-list">
		<form action="<?php echo url_for('tentative_repproved_student/save') ?>" method="POST">
			<h2><?php echo __('Alumnos en condiciones de inscribir en trayectoria:') ?></h2>
			<ul class="sf_admin_actions">
				<li><?php echo link_to(__('Back'), 'schoolyear/index', array('class' => 'sf_admin_action_go_back')) ?></li>
				<li><input type="submit" value="<?php echo __('Save') ?>" /></li>
			</ul>
			<?php echo $form ?>
			<ul class="sf_admin_actions">
				<li><?php echo link_to(__('Back'), 'schoolyear/index', array('class' => 'sf_admin_action_go_back')) ?></li>
				<li><input type="submit" value="<?php echo __('Save') ?>" /></li>
			</ul>
		</form>
		
	</div>

	<div style="margin-top: 1px; clear: both;"></div>
</div>