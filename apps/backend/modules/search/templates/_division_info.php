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
<div class="info_div"><?php echo link_to($object, '@division_show?id='. $object->getId()) ?></div>
<div class="info_text_div">
  <strong><?php echo __("Career:") ?></strong>
  <?php echo $object->getCareer() ?>
</div>
<div class="info_text_div">
  <strong><?php echo $object->countCourses() ? __('Subjects').': '.$object->countCourses() : __('Without subjects') ?></strong>
</div>
<div class="info_text_div">
  <strong><?php echo $object->countStudents() ? __('Students').': '.$object->countStudents() : __('Without students') ?></strong>
</div>