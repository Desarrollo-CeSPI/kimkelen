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
<?php $prefix_name = "day_$day"."_block_".$block ?>
<table>
    <tr>
      <th><?php echo $form[$prefix_name.'_enable']->renderLabel(); ?></th>
      <td><?php echo $form[$prefix_name.'_enable']; ?><?php echo $form[$prefix_name.'_enable']->renderError() ?></td>
    </tr>
    <tr>
      <th><?php echo $form[$prefix_name.'_starts_at']->renderLabel(); ?></th>
      <td><?php echo $form[$prefix_name.'_starts_at']; ?><?php echo $form[$prefix_name.'_starts_at']->renderError(); ?></td>
    </tr>
    <tr>
      <th><?php echo $form[$prefix_name.'_ends_at']->renderLabel(); ?></th>
      <td><?php echo $form[$prefix_name.'_ends_at']; ?><?php echo $form[$prefix_name.'_ends_at']->renderError(); ?></td>
    </tr>
    <tr>
      <th><?php echo $form[$prefix_name.'_classroom_id']->renderLabel(); ?></th>
      <td><?php echo $form[$prefix_name.'_classroom_id']; ?><?php echo $form[$prefix_name.'_classroom_id']->renderError() ?></td>
    </tr>
</table>