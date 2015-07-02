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
<tr>
  <th class='th-subject-name'><?php echo __('Áreas-Materias') ?></th>

  <th colspan="2" ><?php echo __('1C') ?> </th>
  <th colspan="2" ><?php echo __('2C') ?> </th>

  <th rowspan="2"><?php echo __('Prom Anual.') ?></th>
  <th rowspan="2"><?php echo __('Recup Dic.') ?></th>
  <th rowspan="2"><?php echo __('Ex.Dic.') ?></th>
  <th rowspan="2"><?php echo __('Ex.Feb.') ?></th>
  <th rowspan="2"><?php echo __('Ex.Marzo.') ?></th>
  <th rowspan="2"><?php echo __('Calf.Definit.') ?></th>
</tr>

<tr>
  <th class='th-subject-name'></th>
  <th><?php echo __('P.E.P.') ?></th>
  <th><?php echo __('E.I.') ?></th>
  <th ><?php echo __('P.E.P.') ?></th>
  <th><?php echo __('E.I.') ?></th>

  <?php if ($has_attendance_for_subject): ?>
    <th><?php echo __('Inasist. 1°C') ?></th>
    <th><?php echo __('Inasist. 2°C') ?></th>
  <?php endif; ?>
</tr>