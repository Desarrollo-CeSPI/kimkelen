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
<?php use_stylesheet('/bootstrap/css/bootstrap.css', 'last') ?>
<div class="row">
  <div class="col-md-12">
    <div class="col-md-1"></div>
    <div class="col-md-10 container-sombra-exterior container-report-sanctions">
      <div class="title-report-details">
          <span class="title-sanctions"><?php echo __('Admonition details') . ' |'; ?></span> 
          <span><?php echo $student . ' - ' . __('school year') . ' '. $school_year->getYear() ?></span>
      </div>
      
        <?php if(count($student_disciplinary_sanctions) > 0) :?>
        <div class="table-responsive">
			<table class="table table-hover">
			  <tbody>
					  <tr class="success">
						<th><?php echo __('Date') ?></th>
						<th><?php echo __('Reason') ?></th>
						<th><?php echo __('Sanction type') ?></th>
						<th><?php echo __('Total') ?></th>
					  </tr>
					  
					  <?php foreach ($student_disciplinary_sanctions as $student_disciplinary_sanction): ?>
						<tr>
						  <td><?php echo $student_disciplinary_sanction->getFormattedRequestDate(); ?></td>
						  <td><?php echo $student_disciplinary_sanction->getDisciplinarySanctionType(); ?></td>
						  <td><?php echo $student_disciplinary_sanction->getSanctionType(); ?></td>
						  <td><?php echo $student_disciplinary_sanction->getValue(); ?></td>
						</tr>
					  <?php endforeach; ?>
			  </tbody>          
			</table>
		</div>
      <?php else: ?>
      <div class="alert alert-success info-report" role="alert"><?php echo __("Student doesn't have any disciplinary sanctions.") ?></div>
      <?php endif;?>
      
    </div>
    <div class="col-md-1"></div>
  </div>
  <div class="col-md-12 container-buttons">
    <?php echo button_to(__('Go back'), $link, array('class'=>'btn btn-default')) ?>
  </div> 
</div>
