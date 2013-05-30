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

abstract class BaseReportRenderer
{
  const STYLE_BOLD       = 1;
  const STYLE_ITALIC     = 2;
  const STYLE_UNDERLINE  = 4;
  const STYLE_CENTERED   = 8;
  const STYLE_RIGHTED    = 16;
  const STYLE_LEFTED     = 32;
  
  const STYLE_VERTICAL_TOP = 64;
  const STYLE_VERTICAL_CENTER = 128;
  const STYLE_VERTICAL_BOTTOM = 256;
  
  const FONT_SIZE_SMALL  = 8;
  const FONT_SIZE_NORMAL = 10;
  const FONT_SIZE_LARGE  = 12;
  const FONT_SIZE_XLARGE  = 14;

  abstract public function getMimeType();
  abstract public function getHtmlHeaders();

  abstract public function renderRow($data);
  abstract public function renderContent();
}