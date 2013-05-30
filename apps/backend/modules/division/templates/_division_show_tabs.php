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
<a class="tab tab-selected" href="#division_info" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;"><?php echo __('Division')?></a>
<a class="tab" href="#division_courses_info" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;"><?php echo __('Courses')?></a>
<a class="tab" href="#division_configuration_info" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;"><?php echo __('Configuration')?></a>


<fieldset id="division_info">
  <?php echo get_partial('division/division_show_info', array('type' => 'list', 'division' => $division)) ?>
</fieldset>

<fieldset id="division_courses_info">
  <?php echo get_partial('division/division_course_info', array('type' => 'list', 'division' => $division)) ?>
</fieldset>

<fieldset id="division_configuration_info">
  <?php echo get_partial('division/division_configuration_info', array('type' => 'list', 'division' => $division)) ?>
</fieldset>

<script type="text/javascript">
  jQuery('fieldset:gt(0)').hide();
</script>