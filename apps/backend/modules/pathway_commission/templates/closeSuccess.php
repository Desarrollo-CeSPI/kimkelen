<?php
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php use_helper('I18N') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<div id="sf_admin_container">

		<h1><?php echo __('Cerrar curso %course%', array('%course%' => $course))?></h1>

	<div id="sf_admin_content">
		<form action="<?php echo url_for('pathway_commission/saveClose') ?>" method="post">
			<ul class="sf_admin_actions">
				<li><?php echo link_to(__('Back'), "pathway_commission", array('class' => 'sf_admin_action_go_back')) ?></li>

			</ul>
			<input type='hidden' id="id" name="id" value="<?php echo $course->getId()?>">



			<?php foreach ($course->getCourseSubjects() as $course_subject):?>
				<?php include_partial('pathway_commission/course_subject_students', array('course_subject' => $course_subject))?>
			<?php endforeach ?>
		</form>
	</div>
	<div style="margin-top: 1px; clear: both;">
	</div>
</div>