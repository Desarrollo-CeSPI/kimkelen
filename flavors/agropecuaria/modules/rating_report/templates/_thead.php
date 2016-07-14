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

<thead>
  <tr>
    <th rowspan="2"></th>
    <th align="center" rowspan="2"><?php echo __('Nombre y apellido'); ?></th>
    <th align="center" colspan="<?php echo $configuration->getCourseMarks() ?>"><?php echo __('Términos') ?></th>
    <th align="center" rowspan="2" style="width: 10%"><?php echo __('Average') ?></th>
    <th align="center" rowspan="2" style="width: 10%"><?php echo __('Dic.') ?></th>
    <th align="center" rowspan="2" style="width: 10%"><?php echo __('Mar.') ?></th>
    <th align="center" rowspan="2" style="width: 10%"><?php echo __('Asignaturas pendientes') ?></th>
    <th align="center" rowspan="2" style="width: 10%"><?php echo __('Promedio def.') ?></th>
  </tr>

  <tr>
    <?php $marks_count = $configuration->getCourseMarks(); ?>
    <?php if ($configuration->getCourseType() == CourseType::TRIMESTER): ?>
      <?php include_partial('thead_trimester', array('marks_count' => $marks_count)); ?>
    <?php endif; ?>
    <?php if ($configuration->getCourseType() == CourseType::QUATERLY): ?>
      <?php include_partial('thead_quaterly', array('marks_count' => $marks_count)); ?>
    <?php endif; ?>
    <?php if ($configuration->getCourseType() == CourseType::BIMESTER): ?>
      <?php include_partial('thead_bimester', array('marks_count' => $marks_count)); ?>
    <?php endif; ?>
    <?php if ($configuration->getCourseType() == CourseType::QUATERLY_OF_A_TERM): ?>
       <th align="center"><?php echo __('1°'); ?></th>
    <?php endif; ?>
  </tr>
</thead>