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
<?php
/**
 * Form decorator that uses an horizontal table layout style, used for example in
 * CourseSubjectDayForm
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class dcWidgetHorizontalFormatter extends sfWidgetFormSchemaFormatter {
  protected
    $rowFormat       = "<td class='dc_horizontal_column'>\n<table class='dc_horizontal_column_table'><tr>\n<th>\n%error%%label%\n</th></tr>\n<tr><td>\n%help%%field%\n%hidden_fields%</td></tr></table></td>\n",
    $helpFormat      = '<br />%help%',
    $decoratorFormat = '';


}
?>