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
<ul class="objects-shortcuts">
  <li class="title">
    <?php echo __('Últimos creados') ?>
  </li>
  <?php foreach ($shortcuts as $shortcut): ?>
    <li class="shortcut">
      <?php echo link_to(strval($shortcut), url_for(array('sf_route' => $route, 'sf_subject' => $shortcut)), array('class' => 'action show_action')) ?>
    </li>
  <?php endforeach ?>
</ul>
