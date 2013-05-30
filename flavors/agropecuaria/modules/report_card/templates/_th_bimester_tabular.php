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
<th class='th-subject-name'><?php echo __('Áreas-Materias') ?> - <?php echo $number ?>°C</th>
<th><?php echo __('Calificación') ?>
<th><?php echo __('Calif. final') ?></th>
<th><?php echo __('Ev. Diciembre') ?></th>
<th><?php echo __('Ev. Marzo') ?></th>
<th><?php echo __('Calif. definitiva') ?></th>

<?php if (!$division->hasAttendanceForDay()): ?>

  <th><?php echo __('Inasistencias') ?></th>
<?php endif; ?>