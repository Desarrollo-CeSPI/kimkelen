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

class ReportRendererXlsConfiguration
{
  static public function getConfiguration()
  {
    return sfConfig::get('app_report_configuration', array());
  }

  static public function getTempDir()
  {
    $opts = self::getConfiguration();

    return isset($opts['temp_dir'])? $opts['temp_dir'] : '/tmp';
  }

  static public function getOrientation()
  {
    $opts = self::getConfiguration();
    if (isset($opts['orientation']) && $opts['orientation'] == 'landscape')
    {
      return PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE;
    }

    return PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT;
  }

  static public function getTopMargin()
  {
    $opts = self::getConfiguration();
    return isset($opts['top_margin'])? $opts['top_margin'] : 0.393700787;
  }


  static public function getRightMargin()
  {
    $opts = self::getConfiguration();
    return isset($opts['right_margin'])? $opts['right_margin'] : 0.393700787;
  }

  static public function getLeftMargin()
  {
    $opts = self::getConfiguration();
    return isset($opts['left_margin'])? $opts['left_margin'] : 0.393700787;
  }

  static public function getBottomMargin()
  {
    $opts = self::getConfiguration();
    return isset($opts['bottom_margin'])? $opts['bottom_margin'] : 0.393700787;
  }

  static public function getFitToPage()
  {
    $opts = self::getConfiguration();
    return isset($opts['fit_to_page'])? $opts['fit_to_page'] : false;
  }

  static public function getFontSize()
  {
    $opts = self::getConfiguration();
    return isset($opts['font_size'])? $opts['font_size'] : '9';
  }

  static public function getFontName()
  {
    $opts = self::getConfiguration();
    return isset($opts['font_name'])? $opts['font_name'] : 'Arial';
  }

  static public function getPaperSize()
  {
    $opts = self::getConfiguration();
    return isset($opts['paper_size'])? $opts['paper_size'] : PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4;
  }

  static public function getCommonTitle()
  {
    $opts = self::getConfiguration();
    return isset($opts['common_title'])? $opts['common_title'] : false;
  }

  static public function getDateFormat()
  {
    $opts = self::getConfiguration();
    return isset($opts['date_format'])? $opts['date_format'] : false;
  }

  static public function getMaxPerPage()
  {
    $opts       = self::getConfiguration();
    $maxPerPage = isset($opts['max_per_page'])? $opts['max_per_page'] : 1000;

    return $maxPerPage;
  }
}