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
<div class="sf_admin_form_row sf_admin_text">
  <div>
    <?php $groups = $sf_guard_user->getsfGuardUserGroups(); ?>
    <?php if (count($groups) > 0): ?>
      <?php if ($person = PersonPeer::retrieveBySfGuardUser($sf_guard_user)): ?>
        <?php echo 'Persona ' . $person ?>
      <?php endif; ?>
      <table border="1">
        <?php foreach ($groups as $group): ?>
          <?php $group = sfGuardGroupPeer::retrieveByPk($group->getGroupId()); ?>
          <tr><td><strong><?php echo 'Grupo: ' . $group->getName() ?></strong><br><br></td></tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <?php echo __('El usuario no pertenece a ningún grupo'); ?>
    <?php endif; ?>

  </div>
  <div style="margin-top: 1px; clear: both"></div>
</div>