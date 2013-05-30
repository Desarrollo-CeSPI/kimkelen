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
<div id='content' style='font-size: 12px;'>
  <div id='sf_admin_container'>
    <table class="study_plan">
      <thead>
        <tr>
          <th style="width:10%;"><?php echo __('Year') ?></th>
          <th style="width:30%"><?php echo __('Subject') ?></th>
          <th style="width:30%"><?php echo __('Options') ?></th>
          <th style="width:30%; display:none" class="correlative" ><?php echo __('Correlatives') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php for ($y = 1; $y <= $career->getQuantityYears(); $y++): ?>
          <?php include_component('career', 'studyYear', array('career' => $career, 'year' => $y, "school_year" => $school_year)) ?>
        <?php endfor;?>
      </tbody>
    </table>
  </div>
</div>