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
<?php use_helper('I18N')?>
<?php $divisions = $student->getCurrentDivisions()?>
<?php if (count($divisions)): ?>
  <?php echo count($divisions) > 1 ?  __('Currently inscripted in divisions:') : __('Currently inscripted in division:')?>
  <div class="student_divisions">
    <?php foreach( $divisions as $division):?>
    <div><?php echo link_to($division, 'division/show?id=' . $division->getId())?></div>
    <?php endforeach ?>
  </div>
<?php endif ?>
