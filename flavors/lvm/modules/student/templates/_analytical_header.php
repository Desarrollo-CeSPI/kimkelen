<?php /*
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
<div class="report-header">
	<div class="header_row">
		<div class="title" id="header_analytical_data_left">
			<dl class="dl-horizontal">
				<dt><?php echo __("Legajo N°") ?>: </dt>
				<dd class="detail"><?php echo $career_student->getStudent()->getFileNumber($career_student->getCareer()); ?></dd>
				<dt><?php echo __("División") ?>: </dt>
				<dd class="detail"><?php echo implode(", ", $career_student->getStudent()->getCurrentOrLastStudentCareerSchoolYear()->getDivisions()); ?></dd>
			</dl>
		</div>
		<div class="title" id="header_analytical_data_center">
			<?php echo image_tag("kimkelen_logo_analitico.png", array( 'class'=>'school_logo', 'absolute' => true)) ?>
			<?php $school_name = SchoolBehaviourFactory::getInstance()->getSchoolName(); ?>
			<h1><?php echo $school_name ?> <small><?php echo __("Universidad Nacional de La Plata") ?></small></h1>
		</div>


		<div id="header_analytical_data_right" class="title">
			<div>
				<?php echo __("RMN Nº") ?>
				<?php echo ($career_student->getCareer()->getResolutionNumber()) ? $career_student->getCareer()->getResolutionNumber() : '-';?>
			</div>
			<?php if ($analytical->showCertificate()): ?>
				<?php echo __('Certificado N°'); ?>
				<?php echo (isset($analytic)?$analytic->getId():__('S/N')); ?>
			<?php endif; ?>
		</div>


	</div>

	<div class="header_row">
		<?php include_partial('analytical_header_text', array('student' => $career_student->getStudent(), 'career_student' => $career_student)) ?>
	</div>
</div>
