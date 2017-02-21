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
    <?php include_partial('mainFrontend/personal_info', array('person' => $student)) ?>

    <div class="col-md-8">
      <div class="row title-box">
        <div class="col-md-12 title-icon">
          <?php echo image_tag("frontend/book.svg", array('alt' => __('Disciplinary sanctions'))); ?>
          <span class="title-text"> <?php echo __("Disciplinary sanctions");?> - <?php echo $school_year->getYear()?> </span>
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
            <?php $total= 0 ;?>
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th><?php echo __('Sanction type') ?></th>
                  <th><?php echo __('Total') ?></th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($sanctions_type as $st): ?>
                <tr>
                  <td><?php echo $st->getName() ?></td>
                  <td><?php echo $info[$st->getName()] ?></td>
                </tr>
                <?php $total += $info[$st->getName()]; ?>
              <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td> <strong>Total</strong> </td>
                  <td> <strong><?php echo $total ?></strong> </td>
                </tr>
              </tfoot>
            </table>
            <div class="text-center">
              <?php echo link_to(
                '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> ' . __('Show report') . '',
                'student_disciplinary_sanction/showReport?student_id=' . $student->getId(),
                array('class' => 'btn btn-success')
              )?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
