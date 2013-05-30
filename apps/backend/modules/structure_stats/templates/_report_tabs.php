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
<div id="report_tabs">
  <div id="tabs">
    <ul>
      <li id="general_tab" class="selected"><?php echo link_to_function(content_tag('span', __('General')), 'selectReportTab("#general")') ?></li>
      <li id="years_tab"><?php echo link_to_function(content_tag('span', __('Year')), 'selectReportTab("#years")') ?></li>
      <li id="shifts_tab"><?php echo link_to_function(content_tag('span', __('Shifts')), 'selectReportTab("#shifts")') ?></li>
      <li id="shift_divisions_tab"><?php echo link_to_function(content_tag('span', __('Shift division')), 'selectReportTab("#shift_divisions")') ?></li>
      <li id="year_shifts_tab"><?php echo link_to_function(content_tag('span', __('Year shift')), 'selectReportTab("#year_shifts")') ?></li>
      <li id="year_shift_divisions_tab"><?php echo link_to_function(content_tag('span', __('Year shift and division')), 'selectReportTab("#year_shift_divisions")') ?></li>
    </ul>
  </div>
</div>