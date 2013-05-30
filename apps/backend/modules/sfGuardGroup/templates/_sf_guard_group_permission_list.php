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
<div class="sf_admin_form_row">
  <div>
    <label for="sf_guard_group_permmission_list"><?php echo __("Permisos") ?></label>
  </div>
  <?php if(!$sf_guard_group->getsfGuardGroupPermissions()):?>
    <?php echo 'El grupo no tiene permisos' ?>
  <?php else:?>
    <table>
      <?php foreach ($sf_guard_group->getsfGuardGroupPermissions() as $sf_guard_group_permission): ?>
        <tr><td><?php echo $sf_guard_group_permission->getsfGuardPermission() ?></td></tr>
      <?php endforeach ?>
    </table>
  <?php endif?>
  <div style="margin-top: 1px; clear: both"></div>
</div>