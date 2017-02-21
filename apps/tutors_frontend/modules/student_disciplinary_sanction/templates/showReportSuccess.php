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

<div class="row">
  <div class="col-md-12">
    <?php include_partial('mainFrontend/personal_info', array('person' => $student)); ?>

    <div class="col-md-8">
      <div class="row title-box">
        <div class="col-md-12 title-icon">
          <?php echo image_tag("frontend/book.svg", array('alt' => __('Admonition details'))); ?>
          <span class="title-text"> <?php echo __("Admonition details");?> - <?php echo $school_year->getYear()?> </span>
        </div>
      </div>

      <div class="row action-box">
        <div class="col-md-12 text-right">
          <?php echo link_to(
            '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>' . __('Go back') .'',
            $go_back,
            array('class' => 'btn btn btn-primary')
          )?>
        </div>
      </div>

      <div class="data-box">
      <?php if(count($student_disciplinary_sanctions) > 0) :?>
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th><?php echo __('Date') ?></th>
              <th><?php echo __('Reason') ?></th>
              <th><?php echo __('Sanction type') ?></th>
              <th><?php echo __('Valor') ?></th>
            </tr>
          </thead>
          <tbody>
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
      <?php else: ?>
        <div class="alert alert-danger" role="alert"><?php echo __("Student doesn't have any disciplinary sanctions.") ?></div>
      <?php endif;?>
      </div>
    </div>
  </div>
</div>
