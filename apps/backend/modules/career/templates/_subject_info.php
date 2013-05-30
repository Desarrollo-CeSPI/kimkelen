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
<h6 style="text-align: center ;font-size:70px; color:#333333; !important">
<?php if ($sfy->hasCorrelatives()): ?>
  <?php echo __('Correlativas:') ?> <?php echo implode(' | ', array_map(create_function('$o', 'return $o->getCareerSubjectRelatedByCorrelativeCareerSubjectId();'), $sfy->getCorrelativesRelatedByCareerSubjectId())) ?>
<?php endif;?>

<?php if ($sfy->hasOptionalCareerSubjects()): ?>
  <?php echo __('Opciones:') ?> <?php echo implode(' | ', $sfy->getChoiceCareerSubjects()) ?>
<?php endif;?>
</h6>
