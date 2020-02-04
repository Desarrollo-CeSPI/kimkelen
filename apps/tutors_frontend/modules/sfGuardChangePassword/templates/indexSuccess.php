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
 */
?>
<?php use_helper('I18N','Form') ?>
<div class="row">
	<div class="col-md-12">
		<?php $tutor=TutorPeer::retrieveByUsername($sf_user->getUsername()); ?>
		<?php include_partial('mainFrontend/personal_info', array('person'=>$tutor)); ?>
		<div class="col-md-7">
			<div class="row title-box">
				<div class="col-md-12 title-icon">
					<?php echo image_tag("frontend/key.png", array('alt' => __('Califications'))); ?>
					<span class="title-text"> <?php echo __('Change password') ?> </span>
				</div>
			</div>

			<div class="row action-box">
				<div class="col-md-12 text-right">
					<?php echo link_to(
					'<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>' . __('Go back') .'',
					'@homepage',
					array('class' => 'btn btn btn-primary')
					)?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="data-box">
						<?php include_partial('sfGuardChangePassword/form', array('form' => $form)) ?>	  		
					</div>
				</div>
			</div>
		</div>				
	</div>
</div>




