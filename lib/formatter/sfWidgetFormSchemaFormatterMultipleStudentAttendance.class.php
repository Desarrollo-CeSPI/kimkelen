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
 *
 * @package    symfony
 * @subpackage widget
 * @author     Ivan
 * @version    SVN: $Id: sfWidgetFormSchemaFormatterTable.class.php 5995 2007-11-13 15:50:03Z fabien $
 */
class sfWidgetFormSchemaFormatterMultipleStudentAttendance extends sfWidgetFormSchemaFormatter
{
  protected
    $form            = null,
    $rowFormat       = "  %error%
                          %field%
                          %help%
                          %hidden_fields%",
    $errorRowFormat  = "<div class=\"errors\">\n%errors%</div>\n",
    $helpFormat      = '%help%',
    $decoratorFormat = "<div class='attendance_date' ><div id='name_blank'></div>\n  %content%</div>";



}